<?php

class Mailer {
	protected $settings;      // settings for PEAR Mail object

	/**
	 * Send an email message.
	 *
	 * @access  public
	 * @param string $to Recipient email address
	 * @param string $subject Subject line for message
	 * @param string $body Message body
	 * @param string $replyTo Someone to reply to
	 * @param bool $htmlMessage True to send the email as html
	 *
	 * @return  boolean
	 */
	public function send($to, $subject, $body, $replyTo = null, $htmlMessage = false) {
		require_once ROOT_DIR . '/sys/Email/SendGridSetting.php';
		require_once ROOT_DIR . '/sys/CurlWrapper.php';
		$sendGridSettings = new SendGridSetting();
		if ($sendGridSettings->find(true)){
			//Send the email
			//TODO: Do validation of the address
			$curlWrapper = new CurlWrapper();
			$headers = [
				'Authorization: Bearer ' . $sendGridSettings->apiKey,
				'Content-Type: application/json'
			];
			$curlWrapper->addCustomHeaders($headers, false);

			$apiBody = new stdClass();
			$apiBody->personalizations = [];
			$toAddresses = explode(';', $to);
			foreach ($toAddresses as $tmpToAddress){
				$personalization = new stdClass();
				$personalization->to = [];

				$toAddress = new stdClass();
				$toAddress->email = trim($tmpToAddress);
				$personalization->to[] = $toAddress;

				$apiBody->personalizations[] =$personalization;
			}
			$apiBody->from = new stdClass();
			$apiBody->from->email = $sendGridSettings->fromAddress;
			$apiBody->reply_to = new stdClass();
			$apiBody->reply_to->email = (($replyTo != null) ? $replyTo : $sendGridSettings->replyToAddress);
			$apiBody->subject = $subject;
			$apiBody->content = [];
			$content = new stdClass();
			if ($htmlMessage){
				$content->type = 'text/html';
			}else{
				$content->type = 'text/plain';
			}
			$content->value = $body;
			$apiBody->content[] = $content;

			$response = $curlWrapper->curlPostPage('https://api.sendgrid.com/v3/mail/send', json_encode($apiBody));
			if ($response != ''){
				global $logger;
				$logger->log('Error sending email via SendGrid ' . $curlWrapper->getResponseCode() . ' ' . $response, Logger::LOG_ERROR);
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
//		// Validate sender and recipient
//		$validator = new Mail_RFC822();
//		//Allow the to address to be split
//		disableErrorHandler();
//		try{
//			//Validate the address list to make sure we don't get an error.
//			$validator->parseAddressList($to);
//		}catch (Exception $e){
//			return new AspenError('Invalid Recipient Email Address');
//		}
//		enableErrorHandler();
//
//		if (!$validator->isValidInetAddress($from)) {
//			return new AspenError('Invalid Sender Email Address');
//		}
//
//		$headers = array('To' => $to, 'Subject' => $subject,
//		                 'Date' => date('D, d M Y H:i:s O'),
//		                 'Content-Type' => 'text/plain; charset="UTF-8"');
//		if (isset($this->settings['fromAddress'])){
//			$logger->log("Overriding From address, using " . $this->settings['fromAddress'], Logger::LOG_NOTICE);
//			$headers['From'] = $this->settings['fromAddress'];
//			$headers['Reply-To'] = $from;
//		}else{
//			$headers['From'] = $from;
//		}
//		if ($replyTo != null){
//			$headers['Reply-To'] = $replyTo;
//		}
//
//		// Get mail object
//		if ($this->settings['host'] != false){
//			$mailFactory = new Mail();
//			$mail =& $mailFactory->factory('smtp', $this->settings);
//			if ($mail instanceof AspenError) {
//				return $mail;
//			}
//
//			// Send message
//			return $mail->send($to, $headers, $body);
//		}else{
//			//Mail to false just emits the information to screen
//			$formattedMail = '';
//			foreach ($headers as $key => $header){
//				$formattedMail .= $key . ': ' . $header . '<br />';
//			}
//			$formattedMail .= $body;
//			$logger->log("Sending email", Logger::LOG_NOTICE);
//			$logger->log("From = $from", Logger::LOG_NOTICE);
//			$logger->log("To = $to", Logger::LOG_NOTICE);
//			$logger->log($subject, Logger::LOG_NOTICE);
//			$logger->log($formattedMail, Logger::LOG_NOTICE);
//			return true;
//		}

	}
}