<?php

class Core_Update_Manager
{

	protected static $instance = null;

	public static function create()
	{
		if (!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	// ### TODO LICENSE_POINT
	protected function request_server_data($url, $fields = array())
	{

		$uc_url = Phpr::$config->get('UPDATE_CENTER');
		if (!strlen($uc_url))
			throw new Exception('Update server cannot be found');

		$result = null;
		try
		{
			$poststring = array();

			foreach ($fields as $key=>$val)
				$poststring[] = urlencode($key)."=".urlencode($val);

			$poststring = implode('&', $poststring);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://'.$uc_url.'/'.$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$result = curl_exec($ch);

			if (curl_errno($ch))
				throw new Phpr_ApplicationException("Could not connect to the update server");
			else
				curl_close($ch);

		} catch (Exception $ex) {}

		if (!$result || !strlen($result))
			throw new Exception("Error connecting to the update server");


			//throw new Exception("Error connecting to the update server".'http://'.$uc_url.'/'.$url);

		$result_data = false;
		try
		{
			$result_data = @unserialize($result);
		} catch (Exception $ex) {
			throw new Exception("Invalid response from the update server");
		}

		if ($result_data === false)
			throw new Exception("Invalid response from the update server");

		if ($result_data['error'])
			throw new Exception($result_data['error']);

		return $result_data;
	}

	protected function get_module_versions()
	{
		$result = array();

		$modules = Core_Module_Manager::get_modules();
		foreach ($modules as $module)
		{
			$module_info = $module->get_module_info();
			$module_id = mb_strtolower($module_info->id);
			$build = $module_info->get_version();

			$result[$module_id] = $build;
		}

		return $result;
	}

	protected function get_hash()
	{
		$hash = Db_Module_Parameters::get('core', 'hash');
		if (!$hash)
			throw new Phpr_ApplicationException('License information not found');

		$framework = Phpr_SecurityFramework::create();
		return $framework->decrypt(base64_decode($hash));
	}

	public function request_update_list($hash = null)
	{
		$hash = $hash ? $hash : $this->get_hash();

		$fields = array(
			'versions'=>serialize($this->get_module_versions()),
			'url'=>base64_encode(root_url('/', true, 'http'))
		);
		$response = $this->request_server_data('get_update_list/'.$hash, $fields);

		if (!isset($response['data']))
			throw new Phpr_ApplicationException('Invalid server response.');

		if (!count($response['data']))
			Db_Module_Parameters::set('admin', 'updates_available', 0);

		return $response;
	}

	public function update_application($force = false)
	{
		@set_time_limit(3600);

		if ($force)
		{
			$update_list = $this->get_module_versions();
			$fields = array(
				'modules'=>serialize(array_keys($update_list)),
				'url'=>base64_encode(root_url('/', true, 'http'))
			);
		}
		else
		{
			$update_data = $this->request_update_list();
			$update_list = $update_data['data'];

			$fields = array(
				'modules'=>serialize(array_keys($update_list)),
				'url'=>base64_encode(root_url('/', true, 'http'))
			);
		}

		if (!is_writable(PATH_APP) || !is_writable(PATH_APP.'/modules') || !is_writable(PATH_SYSTEM))
			throw new Exception('The directory ('.PATH_APP.') is not writable for PHP.');

		$hash = $this->get_hash();
		$result = $this->request_server_data('get_file_hashes/'.$hash, $fields);
		$file_hashes = $result['data']['file_hashes'];

		if (!is_array($file_hashes))
			throw new Exception("Invalid server response");

		$tmp_path = PATH_APP.'/temp';
		if (!is_writable($tmp_path))
			throw new Exception("Cannot create temporary file. Path is not writable: " .$tmp_path);

		$files = array();
		try
		{
			foreach ($file_hashes as $code=>$file_hash)
			{
				$tmp_file = $tmp_path.'/'.$code.'.arc';
				$result = $this->request_server_data('get_file/'.$hash.'/'.$code);

				$tmp_save_result = false;
				try
				{
					$tmp_save_result = @file_put_contents($tmp_file, $result['data']);
				}
				catch (Exception $ex)
				{
					throw new Exception("Error creating temporary file in ".$tmp_path);
				}

				$files[] = $tmp_file;

				if (!$tmp_save_result)
					throw new Exception("Error creating temporary file in ".$tmp_path);

				$downloaded_hash = md5_file($tmp_file);
				if ($downloaded_hash != $file_hash)
					throw new Exception("Downloaded archive is corrupted. Please try again.");
			}

			foreach ($files as $file)
				File_Zip::unzip(PATH_APP, $file);

			$this->update_cleanup($files);

			Db_Update_Manager::update();

			Db_Module_Parameters::set('admin', 'updates_available', 0);

		}
		catch (Exception $ex)
		{
			$this->update_cleanup($files);

			throw $ex;
		}
	}

	protected function update_cleanup($files)
	{
		foreach ($files as $file)
		{
			if (file_exists($file))
				@unlink($file);
		}
	}


	public function get_updates_flag()
	{
		if (!Phpr::$config->get('AUTO_CHECK_UPDATES', true))
			return false;

		if (Db_Module_Parameters::get('admin', 'updates_available', false))
			return true;

		try
		{
			$last_check = Db_Module_Parameters::get('admin', 'last_update_check', null);
			if (strlen($last_check))
			{
				try
				{
					$last_check_time = new Phpr_DateTime($last_check);

					$check_interval = Phpr::$config->get('UPDATE_CHECK_INTERVAL', 24);
					if (Phpr_DateTime::now()->substract_datetime($last_check_time)->get_hours_total() > $check_interval)
						$last_check = false;
				} catch (Exception $ex) {}
			}

			if (!$last_check)
			{
				try
				{
					$update_data = Core_Update_Manager::create()->request_update_list();
					$updates = $update_data['data'];

					Db_Module_Parameters::set('admin', 'updates_available', count($updates));
				} catch (Exception $ex) {}

				$last_check = Db_Module_Parameters::set('admin', 'last_update_check',
					Phpr_DateTime::now()->format(Phpr_DateTime::universal_datetime_format)
				);
			}
		} catch (Exception $ex) {}
	}


}