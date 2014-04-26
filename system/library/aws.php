<?php

use Aws\S3\S3Client;

class Aws extends Library
{
	/* Aws newms87 account access
	private $access_key_id = "AKIAJ2ZZTS74TPDBTCKA";
	private $secret_access_key = "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";
	*/

	/**
	 * @doc http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_putObject
	 * @doc http://docs.aws.amazon.com/AmazonS3/latest/dev/UploadObjSingleOpPHP.html
	 */

	private $access_key_id = "AKIAJ2LA6XOU2CYEJVNQ";
	private $secret_access_key = "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";

	public function __construct()
	{
		parent::__construct();

	}

	public function backup()
	{
		$s3 = S3Client::factory(array(
			'key'    => $this->access_key_id,
			'secret' => $this->secret_access_key,
		));

		try {
			$image = DIR_IMAGE . 'graveljpg.jpg';

			$result = $s3->putObject(array(
				'Bucket'       => 'OWHP',
				'Key'          => 'graveljpg',
				'SourceFile'   => $image,
				'ContentType'  => 'text/plain',
				'ACL'          => 'private',
				'StorageClass' => 'STANDARD',//or REDUCED_REDUNDANCY
				'Metadata'     => array(
					'owhpbackup' => 'performed',
				)
			));

			echo "RESULT: " . $result['ObjectURL'];
			html_dump($result, 'result');
		} catch (\Aws\S3\Exception\S3Exception $e) {
			// The bucket couldn't be created
			echo "ERROR: " . $e->getMessage();
		}


		exit;
	}
}
