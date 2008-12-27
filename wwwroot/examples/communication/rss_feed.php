<?php require('../../includes/prepend.inc.php');

	// Setup the Feed, itself
	$objRss = new QRssFeed('Examples Site Projects', 'http://examples.qcodo.com/', 'An Example RSS feed of the Qcodo Examples Site Projects');
	$objRss->Image = new QRssImage('http://www.qcodo.com/images/qcodo_smaller.png');
	$objRss->PubDate = new QDateTime(QDateTime::Now);
	
	// Iterate through all the projects, and setup a QRssItem per project
	// Limit it to the "10 most recently started projects"
	foreach ($objProjects = Project::LoadAll(QQ::Clause(QQ::OrderBy(QQN::Project()->StartDate, false), QQ::LimitInfo(10))) as $objProject) {
		$objItem = new QRssItem($objProject->Name,
			'http://examples.qcodo.com/examples/communication/rss.php/' . $objProject->Id,
			$objProject->Description);
	
		$objItem->Author = $objProject->ManagerPerson->FirstName . ' ' . $objProject->ManagerPerson->LastName;
		$objItem->PubDate = $objProject->StartDate;
		$objItem->Guid = $objItem->Link;
		$objItem->GuidPermaLink = true;
		$objItem->AddCategory(new QRssCategory('Some Project Category 1'));
		$objItem->AddCategory(new QRssCategory('Some Project Category 2'));

		$objRss->AddItem($objItem);
	}

	// Output/Run the feed
	// Note that the Run method will reset the output buffer and setup the Headers to output XML,
	// so any HTML or Text outputted until now will be lost.  If for whatever reason you just
	// want the XML, you can call $objRss->GetXml(), which will return the XML string.
	// Also, if you need to change the encoding of the XML, you can do so in QApplication::$EncodingType.
	$objRss->Run();
?>