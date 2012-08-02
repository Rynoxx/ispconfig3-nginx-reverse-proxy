<?php

class vhost {

	/*
	 * the insert function creates the vhost file and link
	 */
	function insert($data, $app, $tpl) {

		if ($data['vhost']['file_new_check'] != 1) {

			/*
			 * the vhost file doesn't exist so we have to create it
			 */
			exec('touch '. $data['vhost']['file_new']);
			$data['vhost']['file_new_check'] = 1;
			$app->log('Creating nginx-rp vhost file: '. $data['vhost']['file_new'], LOGLEVEL_DEBUG);

		}

		if ($data['vhost']['link_new_check'] != 1) {

			/*
			 * the vhost link doesn't exist so we have to create it
			 */
			exec('ln -s '. $data['vhost']['file_new'] .' '. $data['vhost']['link_new']);
			$data['vhost']['link_new_check'] = 1;
			$app->log('Creating nginx-rp vhost symlink: '. $data['vhost']['link_new_check'], LOGLEVEL_DEBUG);

		}


		/*
		 * Write the template content
		 */
		file_put_contents($data['vhost']['file_new'], $tpl);
		$app->log('Writing the nginx-rp vhost file: '. $data['vhost']['file_new'], LOGLEVEL_DEBUG);
		unset($tpl);


		/*
		 * return the $data['vhost'] array
		 */
		return $data['vhost'];

	}


	/*
	 * the update function updates the vhost file and link
	 */
	function update($data, $app, $tpl) {

		/*
		 * check if the site is no longer active
		 */
		if ($data['new']['active'] == 'n') {

			/*
			 * it's not longer active, so we have to tell
			 * the delete function to NOT delete the vhost file
			 * and the insert function, to NOT create the vhost link
			 */
			$data['vhost']['file_old_check'] = 0;
			$data['vhost']['link_new_check'] = 1;


			/*
			 * If the site got renamed and set to inactive,
			 * we still have to remove the old vhost file and link
			 */
			if ($data['old']['domain'] != $data['new']['domain']) $data['vhost']['file_old_check'] = 1;

		}

		/*
		 * The site was renamed, so we have to delete the old vhost and create the new
		 */
		$this->delete($data, $app);
		$this->insert($data, $app, $tpl);

	}


	/*
	 * the delete function deletes the vhost file and link
	 */
	function delete($data, $app, $tpl = '') {

		if ($data['vhost']['file_old_check'] == 1) {

			/*
			 * the vhost file exists so we have to delete it
			 */
			unlink($data['vhost']['file_old']);
			$data['vhost']['file_old_check'] = 0;
			$app->log('Removing nginx-rp vhost file: '. $data['vhost']['file_old'], LOGLEVEL_DEBUG);

		}

		if ($data['vhost']['link_old_check'] == 1) {

			/*
			 * the vhost link exists so we have to delete it
			 */
			unlink($data['vhost']['link_old']);
			$data['vhost']['link_old_check'] = 0;
			$app->log('Removing nginx-rp vhost symlink: '. $data['vhost']['link_old'], LOGLEVEL_DEBUG);

		}


		/*
		 * return the $data['vhost'] array
		 */
		return $data['vhost'];

	}

}