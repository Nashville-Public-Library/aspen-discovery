<?php
require_once ROOT_DIR . '/JSON_Action.php';

class GroupedWork_AJAX extends JSON_Action
{
	/**
	 * Alias of deleteUserReview()
	 *
	 * @return array
	 */
	/** @noinspection PhpUnused */
	function clearUserRating(){
		return $this->deleteUserReview();
	}

	function deleteUserReview(){
		$id = $_REQUEST['id'];
		$result = array('result' => false);
		if (!UserAccount::isLoggedIn()){
			$result['message'] = 'You must be logged in to delete ratings.';
		}else{
			require_once ROOT_DIR . '/sys/LocalEnrichment/UserWorkReview.php';
			$userWorkReview = new UserWorkReview();
			$userWorkReview->groupedRecordPermanentId = $id;
			$userWorkReview->userId = UserAccount::getActiveUserId();
			if ($userWorkReview->find(true)){
				$userWorkReview->delete();
				$result = array('result' => true, 'message' => 'We successfully deleted the rating for you.');
			}else{
				$result['message'] = 'Sorry, we could not find that review in the system.';
			}
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	function forceReindex(){
		require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';

		$id = $_REQUEST['id'];
		$groupedWork = new GroupedWork();
		$groupedWork->permanent_id = $id;
		if ($groupedWork->find(true)){
			$groupedWork->forceReindex(true);

			return array('success' => true, 'message' => 'This title will be indexed again shortly.');
		}else{
			return array('success' => false, 'message' => 'Unable to mark the title for indexing. Could not find the title.');
		}
	}

	function getDescription(){
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$result = [
			'success' => false,
		];
		$id = $_REQUEST['id'];

		$recordDriver = new GroupedWorkDriver($id);
		if ($recordDriver->isValid()){
			$description = $recordDriver->getDescription();
			if (strlen($description) == 0){
				$description = 'Description not provided';
			}
			$description = strip_tags($description, '<a><b><p><i><em><strong><ul><li><ol>');
			$result['success'] = true;
			$result['description'] = $description;
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	function getEnrichmentInfo()
	{
		global $configArray;
		global $interface;
		global $memoryWatcher;

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);
		$interface->assign('recordDriver', $recordDriver);

		$enrichmentResult = array();
		$enrichmentData = $recordDriver->loadEnrichment();
		$memoryWatcher->logMemory('Loaded Enrichment information from Novelist');

		//Process series data
		$titles = array();
		/** @var NovelistData $novelistData */
		if (isset($enrichmentData['novelist'])) {
			$novelistData = $enrichmentData['novelist'];
			if ($novelistData->getSeriesCount() == 0) {
				$enrichmentResult['seriesInfo'] = array('titles' => $titles, 'currentIndex' => 0);
			} else {
				foreach ($novelistData->getSeriesTitles() as $key => $record) {
					$titles[] = $this->getScrollerTitle($record, $key, 'Series');
				}

				$seriesInfo = array('titles' => $titles, 'currentIndex' => $novelistData->getSeriesDefaultIndex());
				$enrichmentResult['seriesInfo'] = $seriesInfo;
			}
			$memoryWatcher->logMemory('Loaded Series information');

			//Process other data from novelist
			if ($novelistData->getSimilarTitleCount() > 0) {
				$interface->assign('similarTitles', $novelistData->getSimilarTitles());
				if ($configArray['Catalog']['showExploreMoreForFullRecords']) {
					$enrichmentResult['similarTitlesNovelist'] = $interface->fetch('GroupedWork/similarTitlesNovelistSidebar.tpl');
				} else {
					$enrichmentResult['similarTitlesNovelist'] = $interface->fetch('GroupedWork/similarTitlesNovelist.tpl');
				}
			}
			$memoryWatcher->logMemory('Loaded Similar titles from Novelist');

			if ($novelistData->getAuthorCount()) {
				$interface->assign('similarAuthors', $novelistData->getAuthors());
				if ($configArray['Catalog']['showExploreMoreForFullRecords']) {
					$enrichmentResult['similarAuthorsNovelist'] = $interface->fetch('GroupedWork/similarAuthorsNovelistSidebar.tpl');
				} else {
					$enrichmentResult['similarAuthorsNovelist'] = $interface->fetch('GroupedWork/similarAuthorsNovelist.tpl');
				}
			}
			$memoryWatcher->logMemory('Loaded Similar authors from Novelist');

			if ($novelistData->getSimilarSeriesCount()) {
				$interface->assign('similarSeries', $novelistData->getSimilarSeries());
				if ($configArray['Catalog']['showExploreMoreForFullRecords']) {
					$enrichmentResult['similarSeriesNovelist'] = $interface->fetch('GroupedWork/similarSeriesNovelistSidebar.tpl');
				} else {
					$enrichmentResult['similarSeriesNovelist'] = $interface->fetch('GroupedWork/similarSeriesNovelist.tpl');
				}
			}
			$memoryWatcher->logMemory('Loaded Similar series from Novelist');
		}

		//Load go deeper options
		//TODO: Additional go deeper options
		global $library;
		if ($library->showGoDeeper == 0){
			$enrichmentResult['showGoDeeper'] = false;
		}else{
			require_once(ROOT_DIR . '/Drivers/marmot_inc/GoDeeperData.php');
			$goDeeperOptions = GoDeeperData::getGoDeeperOptions($recordDriver->getCleanISBN(), $recordDriver->getCleanUPC());
			if (count($goDeeperOptions['options']) == 0){
				$enrichmentResult['showGoDeeper'] = false;
			}else{
				$enrichmentResult['showGoDeeper'] = true;
				$enrichmentResult['goDeeperOptions'] = $goDeeperOptions['options'];
			}
		}
		$memoryWatcher->logMemory('Loaded additional go deeper data');

		//Load Series Summary
		$indexedSeries = $recordDriver->getIndexedSeries();
		$series = $recordDriver->getSeries();
		if (!empty($indexedSeries) || !empty($series)){
			global $library;
			foreach ($library->getGroupedWorkDisplaySettings()->showInMainDetails as $detailOption) {
				$interface->assign($detailOption, true);
			}
			$interface->assign('indexedSeries', $indexedSeries);
			$interface->assign('series', $series);
			$enrichmentResult['seriesSummary'] = $interface->fetch('GroupedWork/series-summary.tpl');
		}

		return $enrichmentResult;
	}

	/** @noinspection PhpUnused */
	function getMoreLikeThis(){
		global $configArray;
		global $memoryWatcher;

		$id = $_REQUEST['id'];

		$enrichmentResult = [
			'similarTitles' => [
				'titles' => []
			],
		];

		//Make sure that the title exists
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$recordDriver = new GroupedWorkDriver($id);
		if ($recordDriver->isValid()){
			//Load Similar titles (from Solr)
			$url = $configArray['Index']['url'];
			require_once ROOT_DIR . '/sys/SolrConnector/GroupedWorksSolrConnector.php';
			$db = new GroupedWorksSolrConnector($url);
			$db->disableScoping();
			$similar = $db->getMoreLikeThis($id);
			$memoryWatcher->logMemory('Loaded More Like This data from Solr');
			// Send the similar items to the template; if there is only one, we need
			// to force it to be an array or things will not display correctly.
			if (isset($similar) && count($similar['response']['docs']) > 0) {
				$similarTitles = array();
				foreach ($similar['response']['docs'] as $key => $similarTitle){
					$similarTitleDriver = new GroupedWorkDriver($similarTitle);
					$similarTitles[] = $similarTitleDriver->getScrollerTitle($key, 'MoreLikeThis');
				}
				$similarTitlesInfo = array('titles' => $similarTitles, 'currentIndex' => 0);
				$enrichmentResult['similarTitles'] = $similarTitlesInfo;
			}
			$memoryWatcher->logMemory('Loaded More Like This scroller data');
		}

		return $enrichmentResult;
	}

	/** @noinspection PhpUnused */
	function getWhileYouWait(){
		global $interface;

		$id = $_REQUEST['id'];

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$groupedWorkDriver = new GroupedWorkDriver($id);
		$whileYouWaitTitles = $groupedWorkDriver->getWhileYouWait();

		$interface->assign('whileYouWaitTitles', $whileYouWaitTitles);

		return [
			'success' => true,
			'title' => translate('While You Wait'),
			'body' => $interface->fetch('GroupedWork/whileYouWait.tpl'),
		];
	}

	/** @noinspection PhpUnused */
	function getYouMightAlsoLike(){
		global $interface;
		global $memoryWatcher;

		$id = $_REQUEST['id'];

		global $library;
		if (!$library->showWhileYouWait){
			$interface->assign('numTitles', 0);
		}else{
			//Get all the titles to ignore, everything that has been rated, in reading history, or that the user is not interested in

			//Load Similar titles (from Solr)
			require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
			require_once ROOT_DIR . '/sys/SolrConnector/GroupedWorksSolrConnector.php';
			/** @var SearchObject_GroupedWorkSearcher $db */
			$searchObject = SearchObjectFactory::initSearchObject();
			$searchObject->init();
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			$searchObject->disableScoping();
			$user = UserAccount::getActiveUserObj();
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			$similar = $searchObject->getMoreLikeThis($id, false, false, 3);
			$memoryWatcher->logMemory('Loaded More Like This data from Solr');
			// Send the similar items to the template; if there is only one, we need
			// to force it to be an array or things will not display correctly.
			if (isset($similar) && count($similar['response']['docs']) > 0) {
				$youMightAlsoLikeTitles = array();
				foreach ($similar['response']['docs'] as $key => $similarTitle){
					$similarTitleDriver = new GroupedWorkDriver($similarTitle);
					$youMightAlsoLikeTitles[] = $similarTitleDriver;
				}
				$interface->assign('numTitles', count($similar['response']['docs']));
				$interface->assign('youMightAlsoLikeTitles', $youMightAlsoLikeTitles);
			}else{
				$interface->assign('numTitles', 0);
			}
			$memoryWatcher->logMemory('Loaded More Like This scroller data');
		}

		return [
			'success' => true,
			'title' => translate('You Might Also Like'),
			'body' => $interface->fetch('GroupedWork/youMightAlsoLike.tpl'),
		];
	}

	function getScrollerTitle($record, $index, $scrollerName){
		$cover = $record['mediumCover'];
		$title = preg_replace("~\\s*([/:])\\s*$~","", $record['title']);
		$series = '';
		if (isset($record['series']) && $record['series'] != null){
			if (is_array($record['series'])){
				foreach($record['series'] as $series){
					if (strcasecmp($series, 'none') !== 0){
						break;
					}else{
						$series = '';
					}
				}
			}else{
				$series = $record['series'];
			}
			if (isset($series)){
				$title .= ' (' . $series ;
				if (isset($record['volume'])){
					$title .= ' Volume ' . $record['volume'];
				}
				$title .= ')';
			}
		}

		if (isset($record['id'])){
			global $interface;
			$interface->assign('index', $index);
			$interface->assign('scrollerName', $scrollerName);
			$interface->assign('id', $record['id']);
			$interface->assign('title', $title);
			$interface->assign('linkUrl', $record['fullRecordLink'] );
			$interface->assign('bookCoverUrl', $record['mediumCover']);
			$interface->assign('bookCoverUrlMedium', $record['mediumCover']);
			$formattedTitle = $interface->fetch('RecordDrivers/GroupedWork/scroller-title.tpl');
		}else{
			$originalId = $_REQUEST['id'];
			$formattedTitle = "<div id=\"scrollerTitle{$scrollerName}{$index}\" class=\"scrollerTitle\" onclick=\"return AspenDiscovery.showElementInPopup('$title', '#noResults{$index}')\">" .
					"<img src=\"{$cover}\" class=\"scrollerTitleCover\" alt=\"{$title} Cover\"/>" .
					"</div>";
					$formattedTitle .= "<div id=\"noResults{$index}\" style=\"display:none\">
					<div class=\"row\">
						<div class=\"result-label col-md-3\">Author: </div>
						<div class=\"col-md-9 result-value notranslate\">
							<a href='/Author/Home?author=\"{$record['author']}\"'>{$record['author']}</a>
						</div>
					</div>
					<div class=\"series row\">
						<div class=\"result-label col-md-3\">Series: </div>
						<div class=\"col-md-9 result-value\">
							<a href=\"/GroupedWork/{$originalId}/Series\">{$series}</a>
						</div>
					</div>
					<div class=\"row related-manifestation\">
						<div class=\"col-sm-12\">
							The library does not own any copies of this title.
						</div>
					</div>
				</div>";
		}

		return array(
			'id' => isset($record['id']) ? $record['id'] : '',
			'image' => $cover,
			'title' => $title,
			'author' => isset($record['author']) ? $record['author'] : '',
			'formattedTitle' => $formattedTitle
		);
	}

	/** @noinspection PhpUnused */
	function getGoDeeperData(){
		require_once(ROOT_DIR . '/Drivers/marmot_inc/GoDeeperData.php');
		$dataType = strip_tags($_REQUEST['dataType']);

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : $_GET['id'];
			// TODO: request id is not always being set by index page.
		$recordDriver = new GroupedWorkDriver($id);
		$upc = $recordDriver->getCleanUPC();
		$isbn = $recordDriver->getCleanISBN();

		$formattedData = GoDeeperData::getHtmlData($dataType, 'GroupedWork', $isbn, $upc);
		return array(
			'formattedData' => $formattedData
		);

	}

	/** @noinspection PhpUnused */
	function getWorkInfo(){
		global $interface;

		//Indicate we are showing search results so we don't get hold buttons
		$interface->assign('displayingSearchResults', true);

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);

		if (!empty($_REQUEST['browseCategoryId'])){ // TODO need to check for $_REQUEST['subCategory'] ??
			// Changed from $_REQUEST['browseCategoryId'] to $_REQUEST['browseCategory'] to be consistent with Browse Category code.
			// TODO Need to see when this comes into action and verify it works as expected. plb 8-19-2015
			require_once ROOT_DIR . '/sys/Browse/BrowseCategory.php';
			$browseCategory = new BrowseCategory();
			$browseCategory->textId = $_REQUEST['browseCategoryId'];
			if ($browseCategory->find(true)){
				$browseCategory->numTitlesClickedOn++;
				$browseCategory->update_stats_only();
			}
		}
		$interface->assign('recordDriver', $recordDriver);

		// if the grouped work consists of only 1 related item, return the record url, otherwise return the grouped-work url
		$relatedRecords = $recordDriver->getRelatedRecords();

		// short version
		if (count($relatedRecords) == 1){
			$firstRecord = reset($relatedRecords);
			$url = $firstRecord->getUrl();
		}else{
			$url =  $recordDriver->getLinkUrl();
		}

		$escapedId = htmlentities($recordDriver->getPermanentId()); // escape for html
		$buttonLabel = translate('Add to list');

		// button template
		$interface->assign('escapeId', $escapedId);
		$interface->assign('buttonLabel', $buttonLabel);
		$interface->assign('url', $url);

		$modalBody = $interface->fetch('GroupedWork/work-details.tpl');
		return array(
			'title' => "<a href='$url'>{$recordDriver->getTitle()}</a>",
			'modalBody' => $modalBody,
			'modalButtons' => "<button onclick=\"return AspenDiscovery.Account.showSaveToListForm(this, 'GroupedWork', '$escapedId');\" class=\"modal-buttons btn btn-primary\" style='float: left'>$buttonLabel</button>"
				."<a href='$url'><button class='modal-buttons btn btn-primary'>" . translate("More Info") . "</button></a>"
		);
	}

	/** @noinspection PhpUnused */
	function rateTitle(){
		require_once(ROOT_DIR . '/sys/LocalEnrichment/UserWorkReview.php');
		if (!UserAccount::isLoggedIn()){
			return array('error'=>'Please login to rate this title.');
		}
		if (empty($_REQUEST['id'])) {
			return array('error'=>'ID for the item to rate is required.');
		}
		if (empty($_REQUEST['rating']) || !ctype_digit($_REQUEST['rating'])) {
			return array('error'=>'Invalid value for rating.');
		}
		$rating = $_REQUEST['rating'];
		//Save the rating
		$workReview = new UserWorkReview();
		$workReview->groupedRecordPermanentId = $_REQUEST['id'];
		$workReview->userId = UserAccount::getActiveUserId();
		if ($workReview->find(true)) {
			if ($rating != $workReview->rating){ // update gives an error if the rating value is the same as stored.
				$workReview->rating = $rating;
				$success = $workReview->update();
			} else {
				// pretend success since rating is already set to same value.
				$success = true;
			}
		} else {
			$workReview->rating = $rating;
			$workReview->review = '';  // default value required for insert statements //TODO alter table structure, null should be default value.
			$workReview->dateRated = time(); // moved to be consistent with add review behaviour
			$success = $workReview->insert();
		}

		if ($success) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$groupedWork->permanent_id = $_REQUEST['id'];
			if ($groupedWork->find(true)){
				$groupedWork->forceReindex();
			}

			// Reset any cached suggestion browse category for the user
			$this->clearMySuggestionsBrowseCategoryCache();

			return array('rating'=>$rating);
		} else {
			return array('error'=>'Unable to save your rating.');
		}
	}

	private function clearMySuggestionsBrowseCategoryCache(){
		// Reset any cached suggestion browse category for the user
		global $memCache;
		global $solrScope;
		foreach (array('0', '1') as $browseMode) { // (Browse modes are set in class Browse_AJAX)
			$key = 'browse_category_system_recommended_for_you_' . UserAccount::getActiveUserId() . '_' . $solrScope . '_' . $browseMode;
			$memCache->delete($key);
		}

	}

	/** @noinspection PhpUnused */
	function getReviewInfo(){
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);
		$isbn = $recordDriver->getCleanISBN();

		//Load external (syndicated reviews)
		require_once ROOT_DIR . '/sys/Reviews.php';
		$externalReviews = new ExternalReviews($isbn);
		$reviews = $externalReviews->fetch();
		global $interface;
		$interface->assign('id', $id);
		$numSyndicatedReviews = 0;
		foreach ($reviews as $providerReviews){
			$numSyndicatedReviews += count($providerReviews);
		}
		$interface->assign('syndicatedReviews', $reviews);

		$userReviews = $recordDriver->getUserReviews();
		foreach ($userReviews as $key => $review){
			if (empty($review->review)){
				unset($userReviews[$key]);
			}
		}
		$interface->assign('userReviews', $userReviews);

		return array(
			'numSyndicatedReviews' => $numSyndicatedReviews,
			'syndicatedReviewsHtml' => $interface->fetch('GroupedWork/view-syndicated-reviews.tpl'),
			'numCustomerReviews' => count($userReviews),
			'customerReviewsHtml' => $interface->fetch('GroupedWork/view-user-reviews.tpl'),
		);
	}

	/** @noinspection PhpUnused */
	function getPromptForReviewForm() {
		$user = UserAccount::getActiveUserObj();
		if ($user) {
			if (!$user->noPromptForUserReviews) {
				global $interface;
				$id      = $_REQUEST['id'];
				if (!empty($id)) {
					$results = array(
						'prompt' => true,
						'title' => 'Add a Review',
						'modalBody' => $interface->fetch("GroupedWork/prompt-for-review-form.tpl"),
						'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.showReviewForm(this, \"{$id}\");'>Submit A Review</button>"
					);
				} else {
					$results = array(
						'error' => true,
						'message' => 'Invalid ID.'
					);
				}
			} else {
				// Option already set to don't prompt, so let's don't prompt already.
				$results = array(
					'prompt' => false
				);
			}
		} else {
			$results = array(
				'error' => true,
				'message' => 'You are not logged in.'
			);
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function setNoMoreReviews(){
		$user = UserAccount::getActiveUserObj();
		if ($user) {
			$user->noPromptForUserReviews = 1;
			$success = $user->update();
			return array('success' => $success);
		}else{
			return ['success' => false];
		}
	}

	/** @noinspection PhpUnused */
	function getReviewForm(){
		global $interface;
		$id = $_REQUEST['id'];
		if (!empty($id)) {
			$interface->assign('id', $id);

			// check if rating/review exists for user and work
			require_once ROOT_DIR . '/sys/LocalEnrichment/UserWorkReview.php';
			$groupedWorkReview                           = new UserWorkReview();
			$groupedWorkReview->userId                   = UserAccount::getActiveUserId();
			$groupedWorkReview->groupedRecordPermanentId = $id;
			if ($groupedWorkReview->find(true)) {
				$interface->assign('userRating', $groupedWorkReview->rating);
				$interface->assign('userReview', $groupedWorkReview->review);
			}

//			$title   = ($library->showFavorites && !$library->showComments) ? 'Rating' : 'Review'; // the library object doesn't seem to have the up-to-date settings.
			$title   = ($interface->get_template_vars('showRatings') && !$interface->get_template_vars('showComments')) ? 'Rating' : 'Review';
			$results = array(
				'title' => $title,
				'modalBody' => $interface->fetch("GroupedWork/review-form-body.tpl"),
				'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.saveReview(\"{$id}\");'>Submit $title</button>"
			);
		} else {
			$results = array(
				'error' => true,
				'message' => 'Invalid ID.'
			);
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function saveReview() {
		$result = array();

		if (UserAccount::isLoggedIn() == false) {
			$result['success'] = false;
			$result['message'] = 'Please login before adding a review.';
		}elseif (empty($_REQUEST['id'])) {
			$result['success'] = false;
			$result['message'] = 'ID for the item to review is required.';
		} else {
			require_once ROOT_DIR . '/sys/LocalEnrichment/UserWorkReview.php';
			$id        = $_REQUEST['id'];
			$rating    = isset($_REQUEST['rating']) ? $_REQUEST['rating'] : '';
			$HadReview = isset($_REQUEST['comment']); // did form have the review field turned on? (may be only ratings instead)
			$comment   = $HadReview ? trim($_REQUEST['comment']) : ''; //avoids undefined index notice when doing only ratings.

			$groupedWorkReview                           = new UserWorkReview();
			$groupedWorkReview->userId                   = UserAccount::getActiveUserId();
			$groupedWorkReview->groupedRecordPermanentId = $id;
			$newReview                                   = true;
			if ($groupedWorkReview->find(true)) { // check for existing rating by user
				$newReview = false;
			}
			// set the user's rating and/or review
			if (!empty($rating) && is_numeric($rating)) $groupedWorkReview->rating = $rating;
			if ($newReview) {
				$groupedWorkReview->review = $HadReview ? $comment : ''; // set an empty review when the user was doing only ratings. (per library settings) //TODO there is no default value in the database.
				$groupedWorkReview->dateRated = time();
				$success = $groupedWorkReview->insert();
			} else {
				if ((!empty($rating) && $rating != $groupedWorkReview->rating) || ($HadReview && $comment != $groupedWorkReview->review)) { // update gives an error if the updated values are the same as stored values.
					if ($HadReview) $groupedWorkReview->review = $comment; // only update the review if the review input was in the form.
					$success = $groupedWorkReview->update();
				} else $success = true; // pretend success since values are already set to same values.
			}
			if (!$success) { // if sql save didn't work, let user know.
				$result['success']  = false;
				$result['message'] = 'Failed to save rating or review.';
			} else { // successfully saved
				$result['success']    = true;
				$result['newReview'] = $newReview;
				$result['reviewId']  = $groupedWorkReview->id;
				global $interface;
				$interface->assign('review', $groupedWorkReview);
				$result['reviewHtml'] = $interface->fetch('GroupedWork/view-user-review.tpl');
			}
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	function getEmailForm(){
		global $interface;
		require_once ROOT_DIR . '/sys/Email/Mailer.php';

		$id = $_REQUEST['id'];
		$interface->assign('id', $id);

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$recordDriver = new GroupedWorkDriver($id);

		$relatedRecords = $recordDriver->getRelatedRecords();
		$interface->assign('relatedRecords', $relatedRecords);
		return array(
				'title' => 'Share via Email',
				'modalBody' => $interface->fetch("GroupedWork/email-form-body.tpl"),
				'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.sendEmail(\"{$id}\"); return false;'>Send Email</button>"
		);
	}

	/** @noinspection PhpUnused */
	function sendEmail()
	{
		global $interface;

		$to = strip_tags($_REQUEST['to']);
		$from = strip_tags($_REQUEST['from']);
		$message = $_REQUEST['message'];

		$id = $_REQUEST['id'];
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$recordDriver = new GroupedWorkDriver($id);
		$interface->assign('recordDriver', $recordDriver);
		$interface->assign('url', $recordDriver->getLinkUrl(true));

		if (isset($_REQUEST['related_record'])){
			$relatedRecord = $_REQUEST['related_record'];
			require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
			$recordDriver = new GroupedWorkDriver($id);

			$relatedRecords = $recordDriver->getRelatedRecords();

			foreach ($relatedRecords as $curRecord){
				if ($curRecord->id == $relatedRecord){
					if (isset($curRecord->callNumber)){
						$interface->assign('callnumber', $curRecord->callNumber);
					}
					if (isset($curRecord->shelfLocation)){
						$interface->assign('shelfLocation', strip_tags($curRecord->shelfLocation));
					}
					$interface->assign('url', $curRecord->getDriver()->getAbsoluteUrl());
					break;
				}
			}
		}

		$subject = translate("Library Catalog Record") . ": " . $recordDriver->getTitle();
		$interface->assign('from', $from);
		$interface->assign('emailDetails', $recordDriver->getEmail());
		$interface->assign('recordID', $recordDriver->getUniqueID());
		if (strpos($message, 'http') === false && strpos($message, 'mailto') === false && $message == strip_tags($message)){
			$interface->assign('message', $message);
			$body = $interface->fetch('Emails/grouped-work-email.tpl');

			require_once ROOT_DIR . '/sys/Email/Mailer.php';
			$mail = new Mailer();
			$emailResult = $mail->send($to, $subject, $body, $from);

			if ($emailResult === true){
				$result = array(
						'result' => true,
						'message' => 'Your email was sent successfully.'
				);
			}elseif (($emailResult instanceof AspenError)){
				$result = array(
						'result' => false,
						'message' => "Your email message could not be sent: {$emailResult}."
				);
			}else{
				$result = array(
						'result' => false,
						'message' => 'Your email message could not be sent due to an unknown error.'
				);
			}
		}else{
			$result = array(
					'result' => false,
					'message' => 'Sorry, we can&apos;t send emails with html or other data in it.'
			);
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function markNotInterested(){
		$result = array(
			'result' => false,
			'message' => "Unknown error.",
		);
		if (UserAccount::isLoggedIn()){
			$id = $_REQUEST['id'];
			require_once ROOT_DIR . '/sys/LocalEnrichment/NotInterested.php';
			$notInterested = new NotInterested();
			$notInterested->userId = UserAccount::getActiveUserId();
			$notInterested->groupedRecordPermanentId = $id;

			if (!$notInterested->find(true)){
				$notInterested->dateMarked = time();
				if ($notInterested->insert()) {

					// Reset any cached suggestion browse category for the user
					$this->clearMySuggestionsBrowseCategoryCache();

					$result = array(
						'result' => true,
						'message' => "You won't be shown this title in the future.",
					);
				}
			}else{
				$result = array(
					'result' => false,
					'message' => "This record was already marked as something you aren't interested in.",
				);
			}
		}else{
			$result = array(
				'result' => false,
				'message' => "Please log in.",
			);
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function clearNotInterested(){
		$idToClear = $_REQUEST['id'];
		require_once ROOT_DIR . '/sys/LocalEnrichment/NotInterested.php';
		$notInterested = new NotInterested();
		$notInterested->userId = UserAccount::getActiveUserId();
		$notInterested->id = $idToClear;
		$result = array('result' => false);
		if ($notInterested->find(true)){
			$notInterested->delete();
			$result = array('result' => true);
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function getProspectorInfo(){
		require_once ROOT_DIR . '/Drivers/marmot_inc/Prospector.php';
		global $interface;
		$id = $_REQUEST['id'];
		$interface->assign('id', $id);

		/** @var SearchObject_GroupedWorkSearcher $searchObject */
		$searchObject = SearchObjectFactory::initSearchObject();
		$searchObject->init();

		// Retrieve Full record from Solr
		if (!($record = $searchObject->getRecord($id))) {
			AspenError::raiseError(new AspenError('Record Does Not Exist'));
		}

		$prospector = new Prospector();

		$searchTerms = array(
				array(
						'lookfor' => $record['title_short'],
						'index' => 'Title'
				),
		);
		if (isset($record['author'])){
			$searchTerms[] = array(
					'lookfor' => $record['author'],
					'index' => 'Author'
			);
		}

		$prospectorResults = $prospector->getTopSearchResults($searchTerms, 10);
		$interface->assign('prospectorResults', $prospectorResults['records']);

		return array(
			'numTitles' => count($prospectorResults),
			'formattedData' => $interface->fetch('GroupedWork/ajax-prospector.tpl')
		);
	}

	/** @noinspection PhpUnused */
	function getSeriesSummary(){
		global $interface;
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);
		$interface->assign('recordDriver', $recordDriver);
		$indexedSeries = $recordDriver->getIndexedSeries();
		$series = $recordDriver->getSeries();
		$result = [
			'result' => false,
			'message' => 'No series exist for this record'
		];
		if (!empty($indexedSeries) || !empty($series)){
			global $library;
			foreach ($library->getGroupedWorkDisplaySettings()->showInSearchResultsMainDetails as $detailOption) {
				$interface->assign($detailOption, true);
			}
			$interface->assign('indexedSeries', $indexedSeries);
			$interface->assign('series', $series);
			$result = [
				'result' => true,
				'seriesSummary' => $interface->fetch('GroupedWork/series-summary.tpl')
			];
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function reloadCover(){
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);

		require_once ROOT_DIR . '/sys/Covers/BookCoverInfo.php';
		$bookCoverInfo = new BookCoverInfo();
		$bookCoverInfo->recordType = 'grouped_work';
		$bookCoverInfo->recordId = $id;
		if ($bookCoverInfo->find(true)){
			$bookCoverInfo->imageSource = '';
			$bookCoverInfo->thumbnailLoaded = 0;
			$bookCoverInfo->mediumLoaded = 0;
			$bookCoverInfo->largeLoaded = 0;
			$bookCoverInfo->update();
		}

		$relatedRecords = $recordDriver->getRelatedRecords(true);
		foreach ($relatedRecords as $record){
			$bookCoverInfo = new BookCoverInfo();
			if (strpos($record->id, ':') > 0){
				list($source, $recordId) = explode(':', $record->id);
				$bookCoverInfo->recordType = $source;
				$bookCoverInfo->recordId = $recordId;
			}else{
				$bookCoverInfo->recordType = $record->source;
				$bookCoverInfo->recordId = $record->id;
			}

			if ($bookCoverInfo->find(true)){
				$bookCoverInfo->imageSource = '';
				$bookCoverInfo->thumbnailLoaded = 0;
				$bookCoverInfo->mediumLoaded = 0;
				$bookCoverInfo->largeLoaded = 0;
				$bookCoverInfo->update();
			}
		}

		return array('success' => true, 'message' => 'Covers have been reloaded.  You may need to refresh the page to clear your local cache.');
	}

	/** @noinspection PhpUnused */
	function getUploadCoverForm(){
		global $interface;

		$id = $_REQUEST['id'];
		$interface->assign('id', $id);

		return array(
			'title' => 'Upload a New Cover',
			'modalBody' => $interface->fetch("GroupedWork/upload-cover-form.tpl"),
			'modalButtons' => "<button class='tool btn btn-primary' onclick='$(\"#uploadCoverForm\").submit()'>Upload Cover</button>"
		);
	}

	/** @noinspection PhpUnused */
	function uploadCover(){
		$result = [
			'success' => false,
			'title' => 'Uploading custom cover',
			'message' => 'Sorry your cover could not be uploaded'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Upload Covers'))){
			if (isset($_FILES['coverFile'])) {
				$uploadedFile = $_FILES['coverFile'];
				if (isset($uploadedFile["error"]) && $uploadedFile["error"] == 4) {
					$result['message'] = "No Cover file was uploaded";
				} else if (isset($uploadedFile["error"]) && $uploadedFile["error"] > 0) {
					$result['message'] =  "Error in file upload for cover " . $uploadedFile["error"];
				} else {
					$id = $_REQUEST['id'];
					global $configArray;
					$destFullPath = $configArray['Site']['coverPath'] . '/original/' . $id . '.png';
					$fileType = $uploadedFile["type"];
					if ($fileType == 'image/png'){
						if (copy($uploadedFile["tmp_name"], $destFullPath)){
							$result['success'] = true;
						}
					}elseif ($fileType == 'image/gif'){
						$imageResource = @imagecreatefromgif($uploadedFile["tmp_name"]);
						if (!$imageResource){
							$result['message'] = 'Unable to process this image, please try processing in an image editor and reloading';
						}else if (@imagepng( $imageResource, $destFullPath, 9)){
							$result['success'] = true;
						}
					}elseif ($fileType == 'image/jpg' || $fileType == 'image/jpeg'){
						$imageResource = @imagecreatefromjpeg($uploadedFile["tmp_name"]);
						if (!$imageResource){
							$result['message'] = 'Unable to process this image, please try processing in an image editor and reloading';
						}else if (@imagepng( $imageResource, $destFullPath, 9)){
							$result['success'] = true;
						}
					}else{
						$result['message'] = 'Incorrect image type.  Please upload a PNG, GIF, or JPEG';
					}
				}
			} else {
				$result['message'] = 'No cover was uploaded, please try again.';
			}
		}
		if ($result['success']){
			$this->reloadCover();
			$result['message'] = 'Your cover has been uploaded successfully';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function reloadIslandora(){
		$id = $_REQUEST['id'];
		$sameCatalogRecordCleared = false;
		$cacheMessage = '';
		require_once ROOT_DIR . '/sys/Islandora/IslandoraSamePikaCache.php';
		//Check for cached links
		$sameCatalogRecordCache = new IslandoraSamePikaCache();
		$sameCatalogRecordCache->groupedWorkId = $id;
		if ($sameCatalogRecordCache->find(true)){
			if ($sameCatalogRecordCache->delete() == 1){
				$sameCatalogRecordCleared = true;
			}else{
				$cacheMessage = 'Could not delete same record cache';
			}

		}else{
			$cacheMessage = 'Data not cached for same record link';
		}

		return array(
				'success' => $sameCatalogRecordCleared,
				'message' => $cacheMessage
		);
	}

	/** @noinspection PhpUnused */
	function getCopyDetails(){
		global $interface;

		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$id = $_REQUEST['id'];
		$recordDriver = new GroupedWorkDriver($id);
		$interface->assign('recordDriver', $recordDriver);

		$recordId = $_REQUEST['recordId'];
		$selectedFormat = $_REQUEST['format'];

		$relatedManifestation = null;
		foreach ($recordDriver->getRelatedManifestations() as $relatedManifestation){
			if ($relatedManifestation->format == $selectedFormat){
				break;
			}
		}
		$interface->assign('itemSummaryId', $id);
		$interface->assign('relatedManifestation', $relatedManifestation);

		if ($recordId != $id){
			$record = $recordDriver->getRelatedRecord($recordId);
			if ($record != null){
				$summary = $record->getItemSummary();
			}else{
				$summary = null;
				foreach ($relatedManifestation->getVariations() as $variation){
					if ($recordId == $id . '_' . $variation->label){
						$summary = $variation->getItemSummary();
						break;
					}
				}
			}
		}else{
			$summary = $relatedManifestation->getItemSummary();
		}
		$interface->assign('summary', $summary);

		$modalBody = $interface->fetch('GroupedWork/copyDetails.tpl');
		return array(
			'title' => translate("Copy Summary"),
			'modalBody' => $modalBody,
		);
	}

	/** @noinspection PhpUnused */
	function getGroupWithForm(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];

		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Manually Group and Ungroup Works'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$id = $_REQUEST['id'];
			$groupedWork->permanent_id = $id;
			if ($groupedWork->find(true)) {
				global $interface;
				$interface->assign('id', $id);
				$interface->assign('groupedWork', $groupedWork);
				$results = array(
					'success' => true,
					'title' => translate("Group this with another work"),
					'modalBody' => $interface->fetch("GroupedWork/groupWithForm.tpl"),
					'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.processGroupWithForm()'>Group</button>"
				);
			} else {
				$results['message'] = "Could not find a work with that id";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function getGroupWithInfo(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Manually Group and Ungroup Works'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$id = $_REQUEST['id'];
			$groupedWork->permanent_id = $id;
			if ($groupedWork->find(true)) {
				$results['success'] = true;
				$results['message'] = "<div class='row'><div class='col-tn-3'>Title</div><div class='col-tn-9'><strong>{$groupedWork->full_title}</strong></div></div>";
				$results['message'] .= "<div class='row'><div class='col-tn-3'>Author</div><div class='col-tn-9'><strong>{$groupedWork->author}</strong></div></div>";
			} else {
				$results['message'] = "Could not find a work with that id";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function processGroupWithForm(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Manually Group and Ungroup Works'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';

			$id = $_REQUEST['id'];
			$originalGroupedWork = new GroupedWork();
			$originalGroupedWork->permanent_id = $id;
			if (!empty($id) && $originalGroupedWork->find(true)){
				$workToGroupWithId = $_REQUEST['groupWithId'];
				$workToGroupWith = new GroupedWork();
				$workToGroupWith->permanent_id = $workToGroupWithId;
				if (!empty($workToGroupWithId) && $workToGroupWith->find(true)){
					if ($originalGroupedWork->grouping_category != $workToGroupWith->grouping_category){
						$results['message'] = "These are different categories of works, cannot group.";
					}else{
						require_once ROOT_DIR . '/sys/Grouping/GroupedWorkAlternateTitle.php';
						$groupedWorkAlternateTitle = new GroupedWorkAlternateTitle();
						$groupedWorkAlternateTitle->permanent_id = $workToGroupWithId;
						$groupedWorkAlternateTitle->alternateAuthor = $originalGroupedWork->author;
						$groupedWorkAlternateTitle->alternateTitle = $originalGroupedWork->full_title;
						$groupedWorkAlternateTitle->addedBy = UserAccount::getActiveUserId();
						$groupedWorkAlternateTitle->dateAdded = time();
						$groupedWorkAlternateTitle->insert();
						$originalGroupedWork->forceReindex(true);
						$results['success'] = true;
						$results['message'] = "Your works have been grouped successfully, the index will update shortly.";
					}
				}else{
					$results['message'] = "Could not find work to group with";
				}
			}else{
				$results['message'] = "Could not find work for original id";
			}

		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function getGroupWithSearchForm(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];

		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Manually Group and Ungroup Works'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$id = $_REQUEST['id'];
			$groupedWork->permanent_id = $id;
			if ($groupedWork->find(true)) {
				global $interface;
				$interface->assign('id', $id);
				$interface->assign('groupedWork', $groupedWork);

				$searchId = $_REQUEST['searchId'];
				/** @var SearchObject_GroupedWorkSearcher $searchObject */
				$searchObject = SearchObjectFactory::initSearchObject();
				$searchObject->init();
				$searchObject = $searchObject->restoreSavedSearch($searchId, false);

				if (!empty($_REQUEST['page'])){
					$searchObject->setPage($_REQUEST['page']);
				}

				$searchResults = $searchObject->processSearch(false, false);
				$availableRecords = [];
				$availableRecords[-1] = translate("Select the primary work");
				$recordIndex = ($searchObject->getPage() - 1) * $searchObject->getLimit();
				foreach ($searchResults['response']['docs'] as $doc){
					$recordIndex++;
					if ($doc['id'] != $id) {
						$primaryWork = new GroupedWork();
						$primaryWork->permanent_id = $doc['id'];
						if ($primaryWork->find(true)){
							if ($primaryWork->grouping_category == $groupedWork->grouping_category){
								$availableRecords[$doc['id']] = "$recordIndex) {$primaryWork->full_title} {$primaryWork->author}";
							}
						}
					}
				}
				$interface->assign('availableRecords', $availableRecords);

				$results = array(
					'success' => true,
					'title' => translate("Group this with another work"),
					'modalBody' => $interface->fetch("GroupedWork/groupWithSearchForm.tpl"),
					'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.processGroupWithForm()'>Group</button>"
				);
			} else {
				$results['message'] = "Could not find a work with that id";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	function getStaffView(){
		$result = [
			'success' => false,
			'message' => 'Unknown error loading staff view'
		];
		$id = $_REQUEST['id'];
		require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
		$recordDriver = new GroupedWorkDriver($id);
		if ($recordDriver->isValid()){
			global $interface;
			$interface->assign('recordDriver', $recordDriver);
			$result = [
				'success' => true,
				'staffView' => $interface->fetch($recordDriver->getStaffView())
			];
		}else{
			$result['message'] = 'Could not find that record';
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function deleteAlternateTitle(){
		$result = [
			'success' => false,
			'message' => 'Unknown error deleting alternate title'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Set Grouped Work Display Information'))) {
			$id = $_REQUEST['id'];
			require_once ROOT_DIR . '/sys/Grouping/GroupedWorkAlternateTitle.php';
			$alternateTitle = new GroupedWorkAlternateTitle();
			$alternateTitle->id = $id;
			if ($alternateTitle->find(true)){
				$alternateTitle->delete();
				$result = [
					'success' => true,
					'message' => "Successfully deleted the alternate title"
				];
			}else{
				$results['message'] = "Could not find the alternate title to delete";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function getDisplayInfoForm(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Set Grouped Work Display Information'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$id = $_REQUEST['id'];
			$groupedWork->permanent_id = $id;
			if ($groupedWork->find(true)) {
				global $interface;
				$interface->assign('id', $id);
				$interface->assign('groupedWork', $groupedWork);

				require_once ROOT_DIR . '/sys/Grouping/GroupedWorkDisplayInfo.php';
				$existingDisplayInfo  = new GroupedWorkDisplayInfo();
				$existingDisplayInfo->permanent_id = $id;
				if ($existingDisplayInfo->find(true)){
					$interface->assign('title', $existingDisplayInfo->title);
					$interface->assign('author', $existingDisplayInfo->author);
					$interface->assign('seriesName', $existingDisplayInfo->seriesName);
					$interface->assign('seriesDisplayOrder', ($existingDisplayInfo->seriesDisplayOrder == 0) ? '' : $existingDisplayInfo->seriesDisplayOrder);
				}else{
					require_once ROOT_DIR . '/RecordDrivers/GroupedWorkDriver.php';
					$recordDriver = new GroupedWorkDriver($id);
					$interface->assign('title', $recordDriver->getTitle());
					$interface->assign('author', $recordDriver->getPrimaryAuthor());
					$series = $recordDriver->getSeries();
					if (!empty($series)){
						$interface->assign('seriesName', $series['seriesTitle']);
						$interface->assign('seriesDisplayOrder', $series['volume']);
					}else{
						$interface->assign('seriesName', '');
						$interface->assign('seriesDisplayOrder', '');
					}
				}

				$results = array(
					'success' => true,
					'title' => translate("Set display information"),
					'modalBody' => $interface->fetch("GroupedWork/groupedWorkDisplayInfoForm.tpl"),
					'modalButtons' => "<button class='tool btn btn-primary' onclick='AspenDiscovery.GroupedWork.processGroupedWorkDisplayInfoForm(\"{$id}\")'>" . translate("Set Display Info") . "</button>"
				);
			} else {
				$results['message'] = "Could not find a work with that id";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function processDisplayInfoForm(){
		$results = [
			'success' => false,
			'message' => 'Unknown Error'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Set Grouped Work Display Information'))) {
			require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
			$groupedWork = new GroupedWork();
			$id = $_REQUEST['id'];
			$groupedWork->permanent_id = $id;
			if ($groupedWork->find(true)) {
				$title = $_REQUEST['title'];
				$author = $_REQUEST['author'];
				$seriesName = $_REQUEST['seriesName'];
				$seriesDisplayOrder = $_REQUEST['seriesDisplayOrder'];
				if (!is_numeric($seriesDisplayOrder)){
					$seriesDisplayOrder = '0';
				}
				if (empty($title) && empty($author) && empty($seriesName) && empty($seriesDisplayOrder)){
					$results['message'] = "Please specify at least one piece of information";
				}else{
					require_once ROOT_DIR . '/sys/Grouping/GroupedWorkDisplayInfo.php';
					$existingDisplayInfo  = new GroupedWorkDisplayInfo();
					$existingDisplayInfo->permanent_id = $id;
					$isNew = true;
					if ($existingDisplayInfo->find(true)){
						$isNew = false;
					}
					$existingDisplayInfo->title = $title;
					$existingDisplayInfo->author = $author;
					$existingDisplayInfo->seriesName = $seriesName;
					$existingDisplayInfo->seriesDisplayOrder = $seriesDisplayOrder;
					if ($isNew) {
						$existingDisplayInfo->addedBy = UserAccount::getActiveUserId();
						$existingDisplayInfo->dateAdded = time();
					}
					$existingDisplayInfo->update();

					$groupedWork->forceReindex();

					$results = [
						'success' => true,
						'message' => 'The display information has been set and the index will update shortly.'
					];
				}
			} else {
				$results['message'] = "Could not find a work with that id";
			}
		}else{
			$results['message'] = "You do not have the correct permissions for this operation";
		}
		return $results;
	}

	/** @noinspection PhpUnused */
	function deleteDisplayInfo(){
		$result = [
			'success' => false,
			'title' => 'Deleting display information',
			'message' => 'Unknown error deleting display info'
		];
		if (UserAccount::isLoggedIn() && (UserAccount::userHasPermission('Set Grouped Work Display Information'))) {
			$id = $_REQUEST['id'];
			require_once ROOT_DIR . '/sys/Grouping/GroupedWorkDisplayInfo.php';
			$existingDisplayInfo = new GroupedWorkDisplayInfo();
			$existingDisplayInfo->permanent_id = $id;
			if ($existingDisplayInfo->find(true)){
				$existingDisplayInfo->delete();
				require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
				$groupedWork = new GroupedWork();
				$groupedWork->permanent_id = $id;
				if ($groupedWork->find(true)){
					$groupedWork->forceReindex(false);
				}
				$result = [
					'success' => true,
					'message' => "Successfully deleted the display info, the index will update shortly."
				];
			}else{
				$result['message'] = "Could not find the display info to delete, it's likely been deleted already";
			}
		}else{
			$result['message'] = "You do not have the correct permissions for this operation";
		}
		return $result;
	}

	/** @noinspection PhpUnused */
	function showSelectDownloadForm()
	{
		global $interface;

		$id = $_REQUEST['id'];
		$fileType = $_REQUEST['type'];
		$interface->assign('fileType', $fileType);
		require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
		require_once ROOT_DIR . '/sys/Grouping/GroupedWorkPrimaryIdentifier.php';
		$groupedWork = new GroupedWork();
		$groupedWork->permanent_id = $id;
		if ($groupedWork->find(true)) {
			$interface->assign('id', $id);

			$groupedWorkPrimaryIdentifier = new GroupedWorkPrimaryIdentifier();
			$groupedWorkPrimaryIdentifier->grouped_work_id = $groupedWork->id;
			$groupedWorkPrimaryIdentifier->find();
			$validFiles = [];
			while ($groupedWorkPrimaryIdentifier->fetch()) {
				require_once ROOT_DIR . '/sys/ILS/RecordFile.php';
				require_once ROOT_DIR . '/sys/File/FileUpload.php';
				$recordFile = new RecordFile();
				$recordFile->type = $groupedWorkPrimaryIdentifier->type;
				$recordFile->identifier = $groupedWorkPrimaryIdentifier->identifier;
				$recordFile->find();
				while ($recordFile->fetch()) {
					$fileUpload = new FileUpload();
					$fileUpload->id = $recordFile->fileId;
					$fileUpload->type = $fileType;
					if ($fileUpload->find(true)) {
						$validFiles[$recordFile->fileId] = $fileUpload->title;
					}
				}
			}
			asort($validFiles);
			$interface->assign('validFiles', $validFiles);

			if ($fileType == 'RecordPDF') {
				$buttonTitle = translate('Download PDF');
			} else {
				$buttonTitle = translate('Download Supplemental File');
			}
			return [
				'title' => 'Select File to download',
				'modalBody' => $interface->fetch("GroupedWork/select-download-file-form.tpl"),
				'modalButtons' => "<button class='tool btn btn-primary' onclick='$(\"#downloadFile\").submit()'>{$buttonTitle}</button>"
			];
		} else {
			return [
				'title' => 'Error',
				'modalBody' => "<div class='alert alert-danger'>Could not find that record</div>",
				'modalButtons' => ""
			];
		}
	}

	/** @noinspection PhpUnused */
	function showSelectFileToViewForm()
	{
		global $interface;

		$id = $_REQUEST['id'];
		$fileType = $_REQUEST['type'];
		$interface->assign('fileType', $fileType);
		require_once ROOT_DIR . '/sys/Grouping/GroupedWork.php';
		require_once ROOT_DIR . '/sys/Grouping/GroupedWorkPrimaryIdentifier.php';
		$groupedWork = new GroupedWork();
		$groupedWork->permanent_id = $id;
		if ($groupedWork->find(true)) {
			$interface->assign('id', $id);

			$groupedWorkPrimaryIdentifier = new GroupedWorkPrimaryIdentifier();
			$groupedWorkPrimaryIdentifier->grouped_work_id = $groupedWork->id;
			$groupedWorkPrimaryIdentifier->find();
			$validFiles = [];
			while ($groupedWorkPrimaryIdentifier->fetch()) {
				require_once ROOT_DIR . '/sys/ILS/RecordFile.php';
				require_once ROOT_DIR . '/sys/File/FileUpload.php';
				$recordFile = new RecordFile();
				$recordFile->type = $groupedWorkPrimaryIdentifier->type;
				$recordFile->identifier = $groupedWorkPrimaryIdentifier->identifier;
				$recordFile->find();
				while ($recordFile->fetch()) {
					$fileUpload = new FileUpload();
					$fileUpload->id = $recordFile->fileId;
					$fileUpload->type = $fileType;
					if ($fileUpload->find(true)) {
						$validFiles[$recordFile->fileId] = $fileUpload->title;
					}
				}
			}
			asort($validFiles);
			$interface->assign('validFiles', $validFiles);

			$buttonTitle = translate('View PDF');
			return [
				'title' => 'Select File to View',
				'modalBody' => $interface->fetch("GroupedWork/select-view-file-form.tpl"),
				'modalButtons' => "<button class='tool btn btn-primary' onclick='$(\"#viewFile\").submit()'>{$buttonTitle}</button>"
			];
		} else {
			return [
				'title' => 'Error',
				'modalBody' => "<div class='alert alert-danger'>Could not find that record</div>",
				'modalButtons' => ""
			];
		}
	}
}
