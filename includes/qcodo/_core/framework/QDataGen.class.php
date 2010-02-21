<?php
	class QDataGen extends QBaseClass {
		/**
		 * Given an array of items, this will randomly select an item from the array based on a set of probabilities.
		 * 
		 * This must be a multidimensional array, where each item is an array itself.  The first item within the subarray
		 * is the item itself that could potentially be selected, and the second item within the subarray is the probability
		 * that it should be selected.
		 * 
		 * Note that the probability calculation is based on adding up the probability numbers of all items.
		 * 
		 * For example, if you specified an array like the following:
		 * 	$mixArray[0] = array('apple', 2);
		 * 	$mixArray[1] = array('orange', 3);
		 * 	$mixArray[2] = array('banana', 5);
		 * this method will have a 20% chance of returning "apple", 30% chance of returning "orange",
		 * and a 50% chance of returning "banana".
		 * 
		 * @param mixed[] $mixArray a multidimensional array of items to select from
		 * @return mixed
		 */
		static public function GenerateFromArrayWithProbabilities($mixArray) {
			$intTotalProbability = 0;
			$intCount = count($mixArray);
			for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
				$intTotalProbability += $mixArray[$intIndex][1];
				$mixArray[$intIndex][2] = $intTotalProbability;
			}

			$intResult = rand(0, $intTotalProbability - 1);
			foreach ($mixArray as $mixItem) {
				if ($intResult < $mixItem[2]) {
					return $mixItem[0];
				}
			}

			// If we are here, then something has gone wrong
			throw new QCallerException('Invalid multidimensional array passed into method');
		}

		/**
		 * Given an array of items, this will randomly select an item from the array.
		 * 
		 * This must be a simple (one-dimensional) array.
		 * @param mixed[] $mixArray a simple (one-dimensional) array of items to select from
		 * @return unknown_type
		 */
		static public function GenerateFromArray($mixArray) {
			return $mixArray[rand(0, count($mixArray) - 1)];
		}

		static protected $ForEachTaskStep = array();

		/**
		 * Displays CLI status information, given a description of what is happening and the total number of steps to be iterated.
		 * 
		 * Meant to be used in conjunction with a foreach loop.  Does not manage within a while loop.  
		 * @param string $strDescription the description of the current task
		 * @param integer $intTotal the total number of iterations or steps for this task
		 * @return void
		 */
		static public function DisplayForEachTaskStart($strDescription, $intTotal) {
			print $strDescription . ' (' . $intTotal . ')... [0]';
			QDataGen::$ForEachTaskStep[$strDescription] = 0;
		}

		/**
		 * Updates CLI status information, iterating to the next step number
		 * @return integer the next step number
		 */
		static public function DisplayForEachTaskNext($strDescription) {
			print str_repeat(chr(8), strlen(QDataGen::$ForEachTaskStep[$strDescription]) + 1);
			QDataGen::$ForEachTaskStep[$strDescription]++;
			print QDataGen::$ForEachTaskStep[$strDescription] . ']';
			return QDataGen::$ForEachTaskStep[$strDescription];
		}
		
		/**
		 * Updates CLI status information, specifying that the current task is done.
		 * @return void
		 */
		static public function DisplayForEachTaskEnd($strDescription, $blnClearText = false) {
			if ($blnClearText) {
				$intToClear = strlen(QDataGen::$ForEachTaskStep[$strDescription]) + 2; // Number and Brackets
				$intToClear += strlen(QDataGen::$ForEachTaskStep[$strDescription]) + 2; // Total and Parens
				$intToClear += 5; // spaces and elipses
				$intToClear += strlen($strDescription);
				print str_repeat(chr(8) . ' ' . chr(8), $intToClear);
			} else {
				print " Done.\r\n";
			}
			
			unset(QDataGen::$ForEachTaskStep[$strDescription]);
		}

		static protected $WhileTaskStep = array();

		/**
		 * Displays and manages a while loop, displaying the description and status to the CLI output.
		 * 
		 * Meant to be used in conjunction with a while loop, managing the boolean while() logic, itself.
		 * @param $strDescription the description of the while task being executed
		 * @param $intTotal the total number of iterations
		 * @return boolean true/false return value to be used within a while() statement
		 */
		static public function DisplayWhileTask($strDescription, $intTotal, $blnClearText = false) {
			if (!array_key_exists($strDescription, QDataGen::$WhileTaskStep)) {
				// Display the Start of the Task, and begin the iteration
				print $strDescription . ' (' . $intTotal . ')... [0]';
				QDataGen::$WhileTaskStep[$strDescription] = 0;
				return true;

			} else {

				// Update the Status and Increment the Step
				print str_repeat(chr(8), strlen(QDataGen::$WhileTaskStep[$strDescription]) + 1);
				QDataGen::$WhileTaskStep[$strDescription]++;
				print QDataGen::$WhileTaskStep[$strDescription] . ']';

				// Continue the Loop if applicable
				if (QDataGen::$WhileTaskStep[$strDescription] < $intTotal) {
					return true;

				// Otherwise, stop the loop and clear the loop state
				} else {
					if ($blnClearText) {
						$intToClear = strlen(QDataGen::$WhileTaskStep[$strDescription]) + 2; // Number and Brackets
						$intToClear += strlen($intTotal) + 2; // Total and Parens
						$intToClear += 5; // spaces and elipses
						$intToClear += strlen($strDescription);
						print str_repeat(chr(8) . ' ' . chr(8), $intToClear);
					} else {
						print " Done.\r\n";
					}
					unset(QDataGen::$WhileTaskStep[$strDescription]);
					return false;
				}
			}
		}

		/**
		 * Generates and returns a random Last Name
		 * @return string
		 */
		static public function GenerateLastName() {
			return QDataGen::$LastNameArray[rand(0, count(QDataGen::$LastNameArray) - 1)];
		}

		/**
		 * Generates and returns a random First Name
		 * @return string
		 */
		static public function GenerateFirstName() {
			// Create FirstNameArray if it is not yet created
			if (!QDataGen::$FirstNameArray) {
				QDataGen::$FirstNameArray = array_merge(QDataGen::$MaleFirstNameArray, QDataGen::$FemaleFirstNameArray);
			}

			return QDataGen::$FirstNameArray[rand(0, count(QDataGen::$FirstNameArray) - 1)];
		}

		/**
		 * Generates and returns a random Middle Initial
		 * @return string
		 */
		static public function GenerateMiddleInitial() {
			return chr(rand(ord('A'), ord('Z')));
		}

		/**
		 * Generates and returns a random Male First Name
		 * @return string
		 */
		static public function GenerateMaleFirstName() {
			return QDataGen::$MaleFirstNameArray[rand(0, count(QDataGen::$MaleFirstNameArray) - 1)];
		}

		/**
		 * Generates and returns a random Female First Name
		 * @return string the randomly-generated Female First Name
		 */
		static public function GenerateFemaleFirstName() {
			return QDataGen::$FemaleFirstNameArray[rand(0, count(QDataGen::$FemaleFirstNameArray) - 1)];
		}
		
		/**
		 * Generates and returns a random City
		 * @return string the randomly-generated city
		 */
		static public function GenerateCity() {
			return QDataGen::$CityArray[rand(0, count(QDataGen::$CityArray) - 1)];
		}
				
		/**
		 * Generates and returns a random Street Address
		 * @return string the randomly-generated street address
		 */
		static public function GenerateStreetAddress() {
			return rand(100, 99999) . ' ' . QDataGen::$CityArray[rand(0, count(QDataGen::$CityArray) - 1)] . ' ' .
				QDataGen::$StreetTypeArray[rand(0, count(QDataGen::$StreetTypeArray) - 1)];
		}

		/**
		 * Generates and returns a random Address 2 line
		 * @return string the randomly-generated Address 2 line
		 */
		static public function GenerateAddressLine2() {
			return QDataGen::$Address2UnitTypeArray[rand(0, count(QDataGen::$Address2UnitTypeArray) - 1)] . ' ' . rand(100, 9999);
		}

		/**
		 * Generates and returns a random US State 2-letter abbreviation
		 * @return string the randomly-generated US State
		 */
		static public function GenerateUsState() {
			return QDataGen::$UsStateArray[rand(0, count(QDataGen::$UsStateArray) - 1)];
		}

		/**
		 * Generates and returns a random Word
		 * @return string the randomly-generated word
		 */
		static public function GenerateWord() {
			return QDataGen::$WordArray[rand(0, count(QDataGen::$WordArray) - 1)];
		}

		/**
		 * Generates and returns a random Title
		 * @param integer $intMinimumWordCount number of words for the title, or if a range, the minimum number of words for the title
		 * @param integer $intMaximumWordCount if a range, the maximum number of words for the title
		 * @return string the randomly-generated title
		 */
		static public function GenerateTitle($intMinimumWordCount, $intMaximumWordCount = null) {
			if (is_null($intMaximumWordCount))
				$intCount = $intMinimumWordCount;
			else
				$intCount = rand($intMinimumWordCount, $intMaximumWordCount);

			if (!($intCount >= 1))
				throw new QCallerException('Requested word count must be greater than 0');

			$strContent = null;
			for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
				$strContent .= QDataGen::GenerateWord() . ' ';
			}
			$strContent = trim($strContent);

			// Fixup due to double-word entries
			while (str_word_count($strContent) > $intCount)
				$strContent = trim(substr($strContent, 0, strrpos($strContent, ' ')));

			return ucwords($strContent);
		}

		/**
		 * Generates and returns random Content based on "Lorem Ipsum" text.  Caller must specify
		 * the number of paragraphs to be generated, where the word count for each individual
		 * paragraph is a random number selected between intMinimumWordsPerParagraph and intMaximumWordsPerParagraph.
		 *   
		 * @param integer $intParagraphCount number of paragraphs to generate, 
		 * @param integer $intMinimumWordsPerParagraph the minimum number of words per paragraph
		 * @param integer $intMaximumWordsPerParagraph the maximum number of words per paragraph
		 * @return string
		 */
		static public function GenerateContent($intParagraphCount, $intMinimumWordsPerParagraph = 20, $intMaximumWordsPerParagraph = 150) {
			$strContent = null;
			for ($intParagraph = 0; $intParagraph < $intParagraphCount; $intParagraph++) {
				$intWordCount = rand($intMinimumWordsPerParagraph, $intMaximumWordsPerParagraph);
				$strParagraph = null;
				
				// Add Sentences
				while (str_word_count($strParagraph) < $intWordCount) {
					$strParagraph .= QDataGen::$LipsumArray[rand(0, count(QDataGen::$LipsumArray) - 1)] . '  ';
				}

				$strParagraph = trim($strParagraph);

				// Remove Words
				while (str_word_count($strParagraph) > $intWordCount) {
					$strParagraph = trim(substr($strParagraph, 0, strrpos($strParagraph, ' ')));
				}

				// Remove Comma (if applicable)
				if (QString::LastCharacter($strParagraph) == ',')
					$strParagraph = trim(substr($strParagraph, 0, strlen($strParagraph) - 1));

				// Add Period (if applicable)
				if (QString::LastCharacter($strParagraph) != '.')
					$strParagraph .= '.';

				$strContent .= $strParagraph . "\r\n\r\n";
			}

			return trim($strContent);
		}

		/**
		 * Generates a random QDateTime given the range of $dttStart and $dttEnd
		 * @param QDateTime $dttStart the start date for the range
		 * @param QDateTime $dttEnd the end date for the range
		 * @return QDateTime
		 */
		static public function GenerateDateTime(QDateTime $dttStart, QDateTime $dttEnd) {
			$intStart = $dttStart->Timestamp;
			$intEnd = $dttEnd->Timestamp;
			$intRand = rand($intStart, $intEnd);
			return QDateTime::FromTimestamp($intRand);
		}

		/**
		 * A very simple / basic random 10-digit telephone number generator.
		 * @return string
		 */
		static public function GeneratePhone() {
			return rand(200, 999) . '-' . rand(200, 999) . '-' . rand(1000, 9999);
		}

		/**
		 * Returns a randomly generated email based on a first name and last name.
		 * @param string $strFirstName first name of the email user
		 * @param string $strLastName last name of the email user
		 * @return string
		 */
		static public function GenerateEmail($strFirstName, $strLastName) {
			$strFirstName = trim(strtolower(str_replace("'", '', str_replace(' ', '', $strFirstName))));
			$strLastName = trim(strtolower(str_replace("'", '', str_replace(' ', '', $strLastName))));

			// three different "styles" of email
			switch (rand(1, 3)) {
				case 1:
					$strEmail = QString::FirstCharacter($strFirstName) . $strLastName . rand(0, 9999) . '@';
					break;
				case 2:
					$strEmail = $strFirstName . QString::FirstCharacter($strLastName) . rand(0, 9999) . '@';
					break;
				case 3:
					$strEmail = $strFirstName . '.' . $strLastName . rand(0, 9999) . '@';
					break;
			}

			return $strEmail . QDataGen::$EmailDomainArray[rand(0, count(QDataGen::$EmailDomainArray) - 1)];
		}

		/**
		 * Returns a randomly generated Home Page URL based on a first name and last name.
		 * @param string $strFirstName first name of the email user
		 * @param string $strLastName last name of the email user
		 * @return string
		 */
		static public function GenerateHomeUrl($strFirstName, $strLastName) {
			return 'http://www.' . QDataGen::GenerateUsername($strFirstName, $strLastName) . '.' .
				QDataGen::$TldArray[rand(0, count(QDataGen::$TldArray) - 1)] . '/';
		}

		/**
		 * Returns a randomly generated Website URL
		 * @return string
		 */
		static public function GenerateWebsiteUrl() {
			$strDomain = QDataGen::GenerateWord();
			$strDomain = str_replace(' ', '', $strDomain);
			return 'http://www.' . $strDomain . '.' .
				QDataGen::$TldArray[rand(0, count(QDataGen::$TldArray) - 1)] . '/';
		}

		/**
		 * Returns a randomly generated "username" based on a first name and last name.
		 * @param string $strFirstName first name of the user
		 * @param string $strLastName last name of the user
		 * @return string
		 */
		static public function GenerateUsername($strFirstName, $strLastName) {
			$strFirstName = trim(strtolower(str_replace("'", '', str_replace(' ', '', $strFirstName))));
			$strLastName = trim(strtolower(str_replace("'", '', str_replace(' ', '', $strLastName))));
			return QString::FirstCharacter($strFirstName) . $strLastName . rand(0, 9999);
		}

		static protected $FirstNameArray;

		static protected $FemaleFirstNameArray = array('Abbey', 'Abbie', 'Abby', 'Abigail', 'Adah', 'Adalia', 'Adda',
			'Addie', 'Addy', 'Adela', 'Adelaide', 'Adele', 'Adelina', 'Adeline', 'Adella', 'Adelle', 'Adie',
			'Adine', 'Adria', 'Adrianne', 'Adrienne', 'Agatha', 'Aggie', 'Aggy', 'Agnes', 'Aileen', 'Ailie',
			'Aimee', 'Airlia', 'Alana', 'Alanna', 'Alarice', 'Alberta', 'Alethea', 'Alex', 'Alexa', 'Alexandra',
			'Alfreda', 'Alexandrina', 'Alice', 'Alida', 'Alisa', 'Alina', 'Aline', 'Alison', 'Alix', 'Allegra',
			'Allene', 'Alley', 'Allie', 'Ally', 'Alma', 'Almeta', 'Almira', 'Aloysia', 'Althea', 'Alva',
			'Alvina', 'Alvita', 'Alysia', 'Alyssa', 'Amabelle', 'Amanda', 'Amara', 'Amaryl', 'Amaryllis', 'Amber',
			'Ambrosine', 'Amity', 'Amorette', 'Amorita', 'Anastasia', 'Anda', 'Andra', 'Andrea', 'Andrina', 'Andromeda',
			'Anella', 'Angel', 'Angela', 'Angelica', 'Angie', 'Anita', 'Annabelle', 'Anne', 'Annetta', 'Annette',
			'Annie', 'Annot', 'Anny', 'Annye', 'Anthea', 'Antoinette', 'Antonia', 'April', 'Arabella', 'Ardelis',
			'Ardella', 'Ardelle', 'Ardis', 'Aretina', 'Ariana', 'Ariella', 'Arilda', 'Arina', 'Arleen', 'Armilla',
			'Artemis', 'Artie', 'Aspasia', 'Astra', 'Astrid', 'Athalia', 'Athena', 'Audrey', 'Augusta', 'Aurelia',
			'Aurora', 'Aury', 'Austine', 'Avis', 'Avril', 'Azalea', 'Babbette', 'Babbie', 'Babette', 'Babs',
			'Bailey', 'Bambi', 'Barbara', 'Barbie', 'Barby', 'Bathsheba', 'Battista', 'Beat', 'Beata', 'Beate',
			'Beatrice', 'Beatrix', 'Beattie', 'Beatty', 'Beck', 'Becky', 'Belinda', 'Bell', 'Bella', 'Belle',
			'Benedicta', 'Benetta', 'Benita', 'Berdine', 'Berna', 'Bernice', 'Berny', 'Bernadette', 'Bernardina', 'Bernardine',
			'Bert', 'Berta', 'Bertha', 'Bertie', 'Bertina', 'Berty', 'Beryl', 'Bess', 'Bessie', 'Bessy',
			'Beth', 'Bethany', 'Bethesda', 'Betsey', 'Betsie', 'Betsy', 'Betta', 'Bette', 'Betti', 'Bettie',
			'Bettina', 'Bettine', 'Betty', 'Bettye', 'Beulah', 'Beverley', 'Beverly', 'Bianca', 'Bice', 'Biddy',
			'Billie', 'Bina', 'Bird', 'Birdie', 'Blair', 'Blanche', 'Blenda', 'Blythe', 'Bobbe', 'Bobbi',
			'Bobbie', 'Bobby', 'Bonnibel', 'Bonnie', 'Brandi', 'Breezy', 'Brenda', 'Briana', 'Bridget', 'Bridie',
			'Brina', 'Brit', 'Brita', 'Brittany', 'Brooke', 'Brunhilde', 'Byrd', 'Byrdie', 'Caddie', 'Caddy',
			'Caitlin', 'Calista', 'Camille', 'Candace', 'Candi', 'Cadida', 'Candy', 'Cara', 'Carissa', 'Carlin',
			'Carla', 'Carleen', 'Carlen', 'Carlene', 'Carlotta', 'Carly', 'Carmel', 'Carmen', 'Carol', 'Carolina',
			'Caroline', 'Carrie', 'Carry', 'Casey', 'Cass', 'Cassandra', 'Cassie', 'Catherine', 'Cathie', 'Cathy',
			'Catty', 'Cecilia', 'Ceeley', 'Celeste', 'Cesca', 'Chantal', 'Charity', 'Charlene', 'Charlotte', 'Chat',
			'Chattie', 'Chatty', 'Chelsea', 'Cherise', 'Cherry', 'Cheryl', 'Chloe', 'Chris', 'Chrissie', 'Chrissy',
			'Christa', 'Christiana', 'Christie', 'Christina', 'Christine', 'Christy', 'Cilla', 'Cindy', 'Cissie', 'Cissy',
			'Clarissa', 'Clem', 'Clemence', 'Clemency', 'Clementina', 'Clementina', 'Clementine', 'Cleo', 'Cleopatra', 'Coba',
			'Cobina', 'Colette', 'Columbina', 'Connie', 'Constance', 'Cora', 'Coral', 'Coralie', 'Corrie', 'Courtney',
			'Cyneburga', 'Cynna', 'Cynthia', 'Cynthie', 'Daff', 'Daffy', 'Dakota', 'Damita', 'Danica', 'Danielle',
			'Daph', 'Daphne', 'Daria', 'Darla', 'Darlene', 'Davida', 'Dawn', 'Debbie', 'Debby', 'Deborah',
			'Debra', 'Debs', 'Deena', 'Deirdre', 'Delia', 'Delilah', 'Dell', 'Della', 'Delle', 'Demetria',
			'Dena', 'Denise', 'Denny', 'Desiree', 'Devin', 'Diana', 'Diane', 'Dinah', 'Dion', 'Dione',
			'Dionetta', 'Dixie', 'Dizzy', 'Dodie', 'Dodo', 'Dolores', 'Doll', 'Dollie', 'Dolly', 'Dominica',
			'Dominique', 'Donna', 'Donalda', 'Donaldina', 'Dora', 'Dore', 'Doreen', 'Doretta', 'Dorette', 'Dori',
			'Doris', 'Dorita', 'Dorothea', 'Dorothy', 'Dorrie', 'Dorthy', 'Dosia', 'Dothy', 'Dottie', 'Dreda',
			'Drina', 'Drucilla', 'Dulce', 'Dulcea', 'Dulcibella', 'Dulcie', 'Dulcinea', 'Durene', 'Eadie', 'Ebony',
			'Eden', 'Effie', 'Echo', 'Edana', 'Edeline', 'Eden', 'Edie', 'Edith', 'Edlyn', 'Edna',
			'Edolie', 'Edwina', 'Effie', 'Eileen', 'Elaine', 'Elberta', 'Eldora', 'Eldoris', 'Eleanor', 'Elektra',
			'Elena', 'Eleonore', 'Elga', 'Elin', 'Elinor', 'Elisa', 'Elise', 'Eliza', 'Elizabeth', 'Ella',
			'Ellen', 'Ellie', 'Elma', 'Eloise', 'Elsa', 'Elsie', 'Elspet', 'Elspie', 'Elva', 'Elvie',
			'Elvira', 'Emelina', 'Emeline', 'Emily', 'Emma', 'Emmeline', 'Emmie', 'Emmy', 'Ephie', 'Eppie',
			'Erika', 'Erlina', 'Erline', 'Erma', 'Ermengarde', 'Erminie', 'Ermentrud', 'Ermentrude', 'Erna', 'Ernestine',
			'Esmeralda', 'Esna', 'Essie', 'Esta', 'Erin', 'Estella', 'Estelle', 'Esther', 'Estra', 'Ethel',
			'Etheldred', 'Etheldreda', 'Etta', 'Ettie', 'Etty', 'Eudora', 'Eugenia', 'Eula', 'Eulalia', 'Eunice',
			'Euphemia', 'Eustacia', 'Evangelina', 'Evelyn', 'Faith', 'Fannie', 'Fanny', 'Fara', 'Farica', 'Farrah',
			'Fatima', 'Faustine', 'Fawn', 'Faye', 'Fayette', 'Fedora', 'Felicia', 'Fern', 'Fidelity', 'Fifi',
			'Fifine', 'Fiona', 'Flavia', 'Fleta', 'Fleur', 'Flora', 'Floren', 'Florence', 'Floretta', 'Florette',
			'Floria', 'Florinda', 'Florine', 'Florrie', 'Florry', 'Floss', 'Flossie', 'Floy', 'Fran', 'Frances',
			'Francesca', 'Francie', 'Francine', 'Frank', 'Frankie', 'Franny', 'Freda', 'Freddie', 'Freddy', 'Frederica',
			'Frida', 'Gabbie', 'Gabby', 'Gabriela', 'Gabriella', 'Gabrielle', 'Gael', 'Gail', 'Gale', 'Garda',
			'Galiana', 'Gaye', 'Gayle', 'Gelsey', 'Gene', 'Geneva', 'Genevieve', 'Genie', 'Georgette', 'Georgia',
			'Georgiana', 'Georgie', 'Georgina', 'Georgy', 'Geraldine', 'Germaine', 'Gerri', 'Gerrie', 'Gerry', 'Gert',
			'Gertie', 'Gertrude', 'Gerty', 'Giacinta', 'Gilda', 'Gill', 'Gillian', 'Gina', 'Ginger', 'Ginnie',
			'Ginny', 'Glad', 'Gladys', 'Glenna', 'Gloria', 'Glynnis', 'Golda', 'Goldie', 'Goldy', 'Grace',
			'Gracie', 'Gredel', 'Greta', 'Gretchen', 'Gretel', 'Grethel', 'Griselda', 'Grissel', 'Grittie', 'Griz',
			'Grizelda', 'Grizzie', 'Guenevere', 'Guinevere', 'Gussie', 'Gusta', 'Gwen', 'Gwenda', 'Gwennie', 'Gwendolen',
			'Gwendoline', 'Gwendolyn', 'Gwennie', 'Gwinny', 'Gwyn', 'Gwyneth', 'Gwynne', 'Gwynneth', 'Gwynnyth', 'Gypsy',
			'Hattie', 'Haley', 'Hannah', 'Harelda', 'Harriet', 'Harriett', 'Harriot', 'Harriott', 'Harley', 'Harmony',
			'Hazel', 'Heather', 'Hedwig', 'Hedy', 'Helen', 'Helena', 'Helga', 'Helma', 'Heloise', 'Hennie',
			'Henny', 'Henrietta', 'Hepsie', 'Hera', 'Hesper', 'Hester', 'Hesther', 'Hettie', 'Hetty', 'Hilary',
			'Hilda', 'Hill', 'Hillary', 'Holly', 'Honey', 'Honora', 'Honoria', 'Hope', 'Hortense', 'Huberta',
			'Hypatia', 'Ianthe', 'Ibby', 'Idalina', 'Idaline', 'Idelle', 'Idola', 'Idona', 'Ignatia', 'Ilana',
			'Ilma', 'Ilse', 'Immy', 'Imogene', 'Ines', 'Inga', 'Ingrid', 'Irene', 'Iris', 'Irma',
			'Isabel', 'Isabella', 'Isabelle', 'Isadora', 'Ivana', 'Ivory', 'Jacinda', 'Jacinta', 'Jackie', 'Jacky',
			'Jacoba', 'Jacobina', 'Jacqueline', 'Jade', 'Jadwiga', 'Jamesina', 'Jamie', 'Jaime', 'Jamie', 'Jana',
			'Jane', 'Janet', 'Janey', 'Janice', 'Janie', 'Janine', 'Janna', 'Jasmine', 'Jazlyn', 'Jazzy',
			'Jean', 'Jeanne', 'Jeanelle', 'Jeanette', 'Jeannette', 'Jeannie', 'Jeannine', 'Jemima', 'Jemma', 'Jennie',
			'Jennifer', 'Jenny', 'Jeri', 'Jerrie', 'Jerry', 'Jess', 'Jessamine', 'Jessica', 'Jessie', 'Jessy',
			'Jewel', 'Jill', 'Jillian', 'Jinny', 'Joan', 'Joannie', 'Jodi', 'Jodie', 'Jody', 'Joyce',
			'Jocelyn', 'Johanna', 'Johnnie', 'Jolene', 'Josepha', 'Josephine', 'Josette', 'Josie', 'Jozy', 'Juanita',
			'Judi', 'Judy', 'Judith', 'Jule', 'Juli', 'Juliana', 'Julia', 'Julie', 'Juliet', 'Julietta',
			'Juliette', 'June', 'Justine', 'Kacey', 'Kali', 'Kara', 'Karen', 'Karena', 'Karlie', 'Kassia',
			'Katarina', 'Kate', 'Kathie', 'Kathy', 'Katharine', 'Katherine', 'Kathleen', 'Katrina', 'Katy', 'Kayla',
			'Kayley', 'Keely', 'Kelsey', 'Kendra', 'Kerri', 'Kimberley', 'Kimberly', 'Kirby', 'Kirsten', 'Kirsty',
			'Kirsty', 'Kittie', 'Kitty', 'Konnie', 'Kristen', 'Kristi', 'Kristina', 'Kristine', 'Kristy', 'Kyla',
			'Laetitia', 'Lakeesha', 'Lala', 'Lalage', 'Lallie', 'Lally', 'Lana', 'Lane', 'Lani', 'Lara',
			'Larina', 'Larissa', 'Laura', 'Laurel', 'Laureen', 'Lauren', 'Laurencia', 'Laurentia', 'Lauretta', 'Lauri',
			'Laurie', 'Laurina', 'Laurinda', 'Laurita', 'Laverna', 'Leah', 'Leigh', 'Leanne', 'Leilani', 'Lemuela',
			'Lena', 'Lenita', 'Leola', 'Leonie', 'Leonora', 'Leslie', 'Leta', 'Leticia', 'Letitia', 'Lettie',
			'Letty', 'Lexine', 'Lexy', 'Liana', 'Libby', 'Lida', 'Liddy', 'Lilah', 'Lilibet', 'Lily',
			'Lilla', 'Lillah', 'Lillian', 'Lillie', 'Lilybell', 'Lina', 'Linda', 'Lindy', 'Linne', 'Linnet',
			'Linette', 'Lindsay', 'Lisa', 'Liza', 'Lisabet', 'Liselotte', 'Lisette', 'Livia', 'Livvy', 'Livy',
			'Liza', 'Lizbeth', 'Lizette', 'Lizzie', 'Lois', 'Lola', 'Lolita', 'Lolly', 'Lora', 'Loralie',
			'Lore', 'Lorelei', 'Loren', 'Lorena', 'Lorene', 'Loretta', 'Lori', 'Lorie', 'Lorraine', 'Lotta',
			'Lotte', 'Lottie', 'Lotty', 'Louella', 'Louie', 'Louisa', 'Louise', 'Lucia', 'Lucie', 'Luciana',
			'Lucille', 'Lucinda', 'Lucky', 'Lucy', 'Luella', 'Lula', 'Lulu', 'Luna', 'Lydia', 'Lynda',
			'Lynn', 'Mabel', 'Mabs', 'Maddie', 'Maddy', 'Madeleine', 'Madeline', 'Madge', 'Madison', 'Mady',
			'Magda', 'Magdalene', 'Maggie', 'Maggy', 'Maisie', 'Mahalia', 'Maia', 'Maisie', 'Malva', 'Mamie',
			'Manda', 'Mandi', 'Mandy', 'Mara', 'Marcella', 'Marcia', 'Marcie', 'Marcy', 'Margaret', 'Marge',
			'Margery', 'Margie', 'Margot', 'Marguerite', 'Margy', 'Maria', 'Marian', 'Maribel', 'Marie', 'Marietta',
			'Mariette', 'Marilla', 'Marilyn', 'Marion', 'Marionette', 'Marjorie', 'Marla', 'Marlene', 'Marnia', 'Marissa',
			'Mart', 'Marta', 'Martha', 'Martie', 'Marty', 'Martina', 'Marvela', 'Mary', 'Mathilda', 'Matilda',
			'Matt', 'Mattie', 'Matty', 'Maud', 'Maude', 'Maudie', 'Maun', 'Maunie', 'Maura', 'Maureen',
			'Mavis', 'Maxine', 'Megan', 'Meggie', 'Meggy', 'Melanie', 'Melinda', 'Melissa', 'Melita', 'Melly',
			'Melody', 'Melvina', 'Merci', 'Mercy', 'Meredith', 'Meris', 'Merle', 'Merry', 'Meta', 'Michelle',
			'Mildred', 'Milicent', 'Millicent', 'Millie', 'Milly', 'Mima', 'Mimi', 'Mina', 'Minella', 'Minerva',
			'Minna', 'Minnie', 'Mirabelle', 'Miranda', 'Miriam', 'Missie', 'Missy', 'Misty', 'Moggy', 'Moira',
			'Moll', 'Mollie', 'Molly', 'Mona', 'Monica', 'Mora', 'Morgan', 'Muriel', 'Myra', 'Nabby',
			'Nada', 'Nadia', 'Nadine', 'Nana', 'Nance', 'Nancey', 'Nanci', 'Nancie', 'Nancy', 'Nancye',
			'Nanette', 'Nanna', 'Nannette', 'Nannie', 'Nanny', 'Naomi', 'Narda', 'Natalie', 'Natica', 'Nathania',
			'Neda', 'Nell', 'Nellie', 'Nelly', 'Neoma', 'Nerissa', 'Nerita', 'Ness', 'Nessa', 'Nessia',
			'Nessie', 'Nettie', 'Netty', 'Nevada', 'Nicki', 'Nicky', 'Nicola', 'Nicole', 'Nicolette', 'Nikki',
			'Nina', 'Ninon', 'Nita', 'Noelle', 'Noellen', 'Nola', 'Noleta', 'Nona', 'Nora', 'Norah',
			'Noreen', 'Norine', 'Norma', 'Nova', 'Nydia', 'Oakes', 'Oakley', 'Obadiah', 'Ocky', 'Octavius',
			'Octavus', 'Odell', 'Ogden', 'Olaf', 'Olin', 'Oliver', 'Ollie', 'Octavia', 'Odelette', 'Odelia',
			'Odette', 'Olethea', 'Olga', 'Olive', 'Olivette', 'Olivia', 'Ollie', 'Olly', 'Olympia', 'Opal',
			'Ophelia', 'Oprah', 'Oralie', 'Oriana', 'Oribel', 'Oriel', 'Orlantha', 'Orlena', 'Orrie', 'Orsola',
			'Orva', 'Paddy', 'Page', 'Pamela', 'Pandora', 'Pansy', 'Panthea', 'Patience', 'Patricia', 'Patsy',
			'Patti', 'Pattie', 'Patty', 'Patty', 'Paula', 'Paulette', 'Pearl', 'Pearlie', 'Peggie', 'Peggoty',
			'Peggy', 'Penelope', 'Penny', 'Persis', 'Petrina', 'Phamie', 'Pheeny', 'Phemie', 'Phemy', 'Pheny',
			'Phil', 'Philana', 'Philippa', 'Philomena', 'Phoebe', 'Phyl', 'Phyllie', 'Phyllis', 'Pippa', 'Polly',
			'Primavera', 'Primrose', 'Pris', 'Priscilla', 'Prissie', 'Prissy', 'Prudence', 'Prudie', 'Prudy', 'Prue',
			'Prunella', 'Rachel', 'Rachie', 'Ramona', 'Randi', 'Randy', 'Rania', 'Raphaela', 'Rashida', 'Reba',
			'Rebecca', 'Reggie', 'Regina', 'Rena', 'Renata', 'Rene', 'Renee', 'Renie', 'Renita', 'Reseda',
			'Rhea', 'Rhoda', 'Rica', 'Ricarda', 'Rita', 'Riva', 'Robbi', 'Robbie', 'Roberta', 'Robertina',
			'Robin', 'Robina', 'Rochelle', 'Roderica', 'Romilda', 'Romola', 'Rona', 'Ronalda', 'Ronky', 'Ronnie',
			'Rora', 'Rosa', 'Rosabel', 'Rosalba', 'Rosalia', 'Rosalie', 'Rosalind', 'Rosamond', 'Rosamund', 'Rosanna',
			'Rosanne', 'Rose', 'Rosemary', 'Rosetta', 'Rosie', 'Rosy', 'Rowena', 'Roxana', 'Roxane', 'Roxanna',
			'Roxanne', 'Roxie', 'Roxy', 'Ruby', 'Rummy', 'Ruth', 'Ruthie', 'Sabina', 'Sabra', 'Sabrina',
			'Sacha', 'Sachi', 'Sadie', 'Salena', 'Salie', 'Sally', 'Salome', 'Samantha', 'Samara', 'Samuela',
			'Sandi', 'Sandra', 'Sandy', 'Sapphire', 'Sara', 'Sarah', 'Sari', 'Sarita', 'Saundra', 'Scarlett',
			'Sela', 'Selda', 'Selena', 'Selene', 'Selima', 'Selma', 'Septima', 'Serafina', 'Seraphine', 'Serena',
			'Shana', 'Shannon', 'Shara', 'Shari', 'Sharley', 'Sharon', 'Shary', 'Shawn', 'Sheba', 'Sheila',
			'Sherri', 'Sherrie', 'Sherry', 'Shirl', 'Shirley', 'Sibie', 'Sibley', 'Sybil', 'Sibyl', 'Sidra',
			'Silver', 'Simona', 'Sindy', 'Sirena', 'Sissie', 'Sissy', 'Sonia', 'Sonya', 'Sophia', 'Sophie',
			'Sophy', 'Stacey', 'Stacia', 'Stacy', 'Stella', 'Stephanie', 'Stormy', 'Suke', 'Sukey', 'Sukie',
			'Suky', 'Sula', 'Summer', 'Susan', 'Susanna', 'Susannah', 'Susanne', 'Susie', 'Susy', 'Suzy',
			'Swanhilda', 'Sydna', 'Sydney', 'Sylvana', 'Silvia', 'Sylvia', 'Sylvie', 'Tabby', 'Tabitha', 'Talia',
			'Tamara', 'Tammy', 'Tanya', 'Tanzine', 'Tara', 'Tatum', 'Tave', 'Tavy', 'Teena', 'Teenie',
			'Templa', 'Teresa', 'Teri', 'Terri', 'Terry', 'Tertia', 'Tess', 'Tessa', 'Tessie', 'Tetsy',
			'Tetty', 'Thadine', 'Thalia', 'Thea', 'Theda', 'Thelma', 'Theo', 'Theodora', 'Theodosia', 'Theola',
			'Theresa', 'Thirza', 'Thomasa', 'Thomasina', 'Thomasine', 'Thora', 'Tibbie', 'Tibby', 'Tiffany', 'Tilda',
			'Tillie', 'Tilly', 'Timothea', 'Tina', 'Tisha', 'Titia', 'Toinette', 'Toni', 'Tonia', 'Tonie',
			'Tony', 'Tonya', 'Tracy', 'Tressa', 'Tric', 'Tricia', 'Trina', 'Trish', 'Trisha', 'Trissie',
			'Trista', 'Trix', 'Trixie', 'Trudie', 'Trudy', 'Valentina', 'Valeria', 'Valerie', 'Valora', 'Vanessa',
			'Vania', 'Vanny', 'Vara', 'Vashti', 'Vaughnie', 'Veda', 'Vedette', 'Veleda', 'Venitia', 'Vera',
			'Verda', 'Verona', 'Veronica', 'Vick', 'Vicki', 'Vickie', 'Vicky', 'Victoria', 'Vida', 'Vidette',
			'Vikki', 'Vina', 'Vinnie', 'Vinny', 'Viola', 'Violet', 'Vira', 'Virgie', 'Virginia', 'Vita',
			'Vivian', 'Voletta', 'Wanda', 'Wenda', 'Wendy', 'Wenona', 'Whitney', 'Wilda', 'Willa', 'Willette',
			'Willow', 'Wilhelmina', 'Wylma', 'Wilona', 'Winifred', 'Winona', 'Wynette', 'Wynne');

		static protected $MaleFirstNameArray = array('Aaron', 'Abbott', 'Abel', 'Abie', 'Abner', 'Abraham', 'Abram',
			'Adal', 'Adam', 'Adalbert', 'Addis', 'Addison', 'Adley', 'Adolf', 'Adolph', 'Adolphus', 'Adrian',
			'Aiden', 'Aiken', 'Ajax', 'Alan', 'Alastair', 'Alban', 'Albe', 'Albern', 'Albert', 'Albin',
			'Albion', 'Alden', 'Aldis', 'Aldrich', 'Alec', 'Aleck', 'Alex', 'Alexander', 'Alfie', 'Alfonso',
			'Alfred', 'Alfy', 'Alger', 'Algernon', 'Algie', 'Algy', 'Alick', 'Allan', 'Allie', 'Ally',
			'Alois', 'Alonso', 'Aloys', 'Aloysius', 'Alphonso', 'Alroy', 'Alston', 'Alton', 'Alvin', 'Ambie',
			'Ambrose', 'Amery', 'Amos', 'Andie', 'Andrew', 'Andy', 'Aneurin', 'Angell', 'Angelo', 'Angus',
			'Ansel', 'Anthony', 'Antony', 'Apollo', 'Arch', 'Archer', 'Archie', 'Archibald', 'Archy', 'Ardon',
			'Arlen', 'Armand', 'Arne', 'Arney', 'Arnie', 'Arno', 'Arnold', 'Arny', 'Arrian', 'Artemis',
			'Arth', 'Arthur', 'Artie', 'Arty', 'Arvel', 'Asher', 'Atwater', 'Atwood', 'Aubrey', 'Augie',
			'August', 'Austin', 'Augustus', 'Avery', 'Axel', 'Bailey', 'Baird', 'Baldie', 'Baldwin', 'Baldy',
			'Barclay', 'Barn', 'Barnabas', 'Barnaby', 'Barnard', 'Barnet', 'Barnett', 'Barney', 'Barnie', 'Baron',
			'Barrett', 'Barrie', 'Barry', 'Bart', 'Bartholomew', 'Bartle', 'Bartlett', 'Bartley', 'Bartram', 'Barty',
			'Basie', 'Basil', 'Bass', 'Batte', 'Batty', 'Baxter', 'Bayard', 'Belden', 'Benedict', 'Benjamin',
			'Benjie', 'Benjy', 'Bennie', 'Benny', 'Benton', 'Bernard', 'Berney', 'Bernie', 'Bert', 'Bertie',
			'Berton', 'Bertram', 'Berty', 'Berwin', 'Beverley', 'Beverly', 'Bevis', 'Bill', 'Billie', 'Billy',
			'Birdie', 'Blaine', 'Blair', 'Blake', 'Blandon', 'Bobie', 'Boby', 'Bogdan', 'Bond', 'Booth',
			'Boris', 'Bowen', 'Bowie', 'Boyce', 'Boyd', 'Boyden', 'Brad', 'Braden', 'Bradley', 'Bram',
			'Brand', 'Brandon', 'Brant', 'Brent', 'Bret', 'Brian', 'Brice', 'Brigham', 'Brock', 'Broderick',
			'Brooke', 'Bruce', 'Brucie', 'Bruno', 'Bryant', 'Buck', 'Burgess', 'Burke', 'Burl', 'Burleigh',
			'Burt', 'Burton', 'Byrd', 'Byrdie', 'Byron', 'Cadman', 'Caesar', 'Calder', 'Cale', 'Caleb',
			'Calvert', 'Calvin', 'Caldwell', 'Cameron', 'Carl', 'Carlin', 'Carlisle', 'Carlo', 'Carlton', 'Carmine',
			'Carney', 'Carroll', 'Carter', 'Carver', 'Cary', 'Casimir', 'Casimiro', 'Casey', 'Caspar', 'Casper',
			'Cecil', 'Cedric', 'Ceese', 'Chad', 'Chadwick', 'Chalmers', 'Chandler', 'Channing', 'Chapin', 'Chapman',
			'Charles', 'Charley', 'Charlie', 'Charly', 'Chatwin', 'Chauncey', 'Chester', 'Chick', 'Chilla', 'Chippy',
			'Cholly', 'Chris', 'Christian', 'Christie', 'Christopher', 'Christy', 'Chuck', 'Clare', 'Clarence', 'Clark',
			'Clarry', 'Claud', 'Claude', 'Claudie', 'Clay', 'Clayton', 'Clem', 'Clemens', 'Clement', 'Clif',
			'Clifford', 'Clive', 'Clyde', 'Coleman', 'Colin', 'Colley', 'Collier', 'Collin', 'Colton', 'Connie',
			'Conan', 'Conrad', 'Conroy', 'Conway', 'Corbin', 'Cornelius', 'Corney', 'Corny', 'Corey', 'Corwin',
			'Cosmo', 'Crispinian', 'Crispin', 'Crosby', 'Cuddie', 'Culbert', 'Culver', 'Curt', 'Curtis', 'Cuthbert',
			'Cyril', 'Cyro', 'Cyrus', 'Dale', 'Dallas', 'Dalton', 'Damon', 'Dandy', 'Daniel', 'Danny',
			'Dante', 'Darcy', 'Darian', 'Darell', 'Dave', 'Davey', 'Davie', 'David', 'Davin', 'Davy',
			'Dean', 'Dekker', 'Delbert', 'Delmar', 'Denis', 'Denley', 'Dennis', 'Denny', 'Derek', 'Derrick',
			'Derry', 'Derwin', 'Desmond', 'Devin', 'Dexter', 'Dick', 'Dickie', 'Dickon', 'Dicky', 'Dillon',
			'Dion', 'Dionysios', 'Dionysos', 'Dirk', 'Dixie', 'Dixon', 'Dobbin', 'Doddy', 'Dode', 'Dolly',
			'Dolph', 'Dolphus', 'Dominick', 'Donald', 'Donnie', 'Donny', 'Dorian', 'Doug', 'Douglas', 'Doyle',
			'Drake', 'Drew', 'Driscoll', 'Duggie', 'Dudley', 'Duke', 'Dunc', 'Duncan', 'Durwin', 'Dwayne',
			'Dwight', 'Earl', 'Eaton', 'Eben', 'Ebenezer', 'Ecky', 'Edan', 'Eddie', 'Eddy', 'Edgar',
			'Edmund', 'Edom', 'Edric', 'Edward', 'Edwin', 'Egan', 'Egbert', 'Elbert', 'Eldon', 'Eldwin',
			'Elias', 'Elijah', 'Elisha', 'Ellick', 'Elliott', 'Elmer', 'Elmy', 'Elroy', 'Elton', 'Emanuel',
			'Emerson', 'Emery', 'Emil', 'Emmanuel', 'Emmett', 'Ephraim', 'Erasmus', 'Erastus', 'Eric', 'Erie',
			'Ernest', 'Ernie', 'Errol', 'Erwin', 'Ethan', 'Ethelbert', 'Eugene', 'Eustace', 'Evan', 'Everett',
			'Ezekiel', 'Ezra', 'Fabian', 'Fabron', 'Fairfax', 'Falkner', 'Farley', 'Farman', 'Farrell', 'Federico',
			'Federigo', 'Felix', 'Fenton', 'Ferd', 'Ferdie', 'Ferdinand', 'Fergie', 'Fergus', 'Ferguson', 'Ferris',
			'Fitzgerald', 'Fleming', 'Fletcher', 'Floyd', 'Floydy', 'Forrest', 'Foster', 'Francis', 'Fran', 'Francie',
			'Frank', 'Frankie', 'Franky', 'Frasier', 'Fred', 'Freddie', 'Freddy', 'Frederic', 'Frederick', 'Freeman',
			'Fritz', 'Gabe', 'Gabey', 'Gabriel', 'Gage', 'Gail', 'Gale', 'Galvin', 'Gardner', 'Garret',
			'Garrett', 'Garrick', 'Garry', 'Gary', 'Garth', 'Gavin', 'Gaylord', 'Gene', 'Geoff', 'Geoffrey',
			'Geordie', 'George', 'Georgie', 'Georgy', 'Gerald', 'Gerard', 'Gerrie', 'Gerry', 'Gersham', 'Gibbie',
			'Gideon', 'Gifford', 'Gilbert', 'Giles', 'Gill', 'Gillian', 'Gillie', 'Gilmer', 'Gilroy', 'Giovanni',
			'Glenn', 'Goddard', 'Godfrey', 'Godwin', 'Graham', 'Grant', 'Grayson', 'Greg', 'Gregg', 'Gregory',
			'Gresham', 'Grig', 'Griswold', 'Grosvenor', 'Grover', 'Gunther', 'Gussie', 'Gussy', 'Gust', 'Gustavus',
			'Gustus', 'Hadden', 'Hadley', 'Hadwin', 'Haines', 'Halbert', 'Halden', 'Hale', 'Halen', 'Hall',
			'Halsey', 'Hamlin', 'Hank', 'Hanley', 'Hans', 'Harcourt', 'Hardy', 'Harlan', 'Harley', 'Harris',
			'Harrison', 'Harold', 'Harry', 'Hartley', 'Harve', 'Harvey', 'Hauk', 'Hayden', 'Hayes', 'Heath',
			'Heck', 'Hector', 'Henny', 'Henry', 'Herb', 'Herbert', 'Herbie', 'Herman', 'Herwin', 'Hilary',
			'Hildebrand', 'Hill', 'Hillary', 'Hilliard', 'Hilton', 'Hiram', 'Hobart', 'Hodge', 'Holbrook', 'Holden',
			'Homer', 'Horace', 'Horatio', 'Hosea', 'Howard', 'Howel', 'Howell', 'Howie', 'Huber', 'Hubert',
			'Hugh', 'Hughie', 'Hugo', 'Humph', 'Humphrey', 'Humphry', 'Hunter', 'Hyman', 'Ichabod', 'Ignace',
			'Ignatius', 'Igor', 'Ikey', 'Ingram', 'Irvin', 'Irving', 'Isaac', 'Isaak', 'Isaiah', 'Isidore',
			'Israel', 'Ivan', 'Ivar', 'Iver', 'Ives', 'Izzy', 'Jack', 'Jackey', 'Jacky', 'Jacob',
			'Jaikie', 'Jake', 'Jamal', 'James', 'Jamie', 'Jared', 'Jarvis', 'Jason', 'Jasper', 'Jean',
			'Jeff', 'Jeffery', 'Jeffrey', 'Jemmie', 'Jemmy', 'Jeremiah', 'Jeremy', 'Jerold', 'Jerome', 'Jerry',
			'Jess', 'Jesse', 'Jessie', 'Jill', 'Jillian', 'Jimie', 'Jimmy', 'Jody', 'Joey', 'Joel',
			'John', 'Johnie', 'Johnny', 'Jolly', 'Jonah', 'Jonas', 'Jonathan', 'Joney', 'Jonny', 'Jonty',
			'Jordan', 'Jose', 'Joseph', 'Josh', 'Joshua', 'Jule', 'Julian', 'Juliun', 'Julius', 'Justin',
			'Kane', 'Karsten', 'Kasimir', 'Keaton', 'Keene', 'Keegan', 'Keith', 'Kelsey', 'Kelvin', 'Kendall',
			'Kendrick', 'Kenneth', 'Kennie', 'Kenny', 'Kent', 'Kenway', 'Kenyon', 'Kerry', 'Kerwin', 'Kester',
			'Kevin', 'Kiefer', 'Kiki', 'Kilby', 'Kilian', 'Kimball', 'Kingsley', 'Kirby', 'Kirk', 'Kitto',
			'Kody', 'Konrad', 'Kris', 'Kurt', 'Kyle', 'Laban', 'Lachie', 'Lachlan', 'Lachy', 'Lamar',
			'Lambert', 'Lamont', 'Lamy', 'Lance', 'Lancelot', 'Landon', 'Landry', 'Lane', 'Langley', 'Lani',
			'Lanny', 'Larry', 'Lars', 'Latimer', 'Launce', 'Launcelot', 'Lauren', 'Laurence', 'Laurie', 'Lawrence',
			'Lawrie', 'Lazarus', 'Leander', 'Leighton', 'Leith', 'Leland', 'Lemuel', 'Lennie', 'Lenny', 'Leon',
			'Leonard', 'Leopold', 'Leroy', 'Leslie', 'Lester', 'Levi', 'Lewie', 'Lexy', 'Lige', 'Lincoln',
			'Lionel', 'Lish', 'Llelo', 'Llew', 'Llewellyn', 'Llewelyn', 'Lloyd', 'Logan', 'Lombard', 'Lonnie',
			'Lonny', 'Loot', 'Lorenzo', 'Lori', 'Loring', 'Louie', 'Louis', 'Lewis', 'Lowell', 'Lowrie',
			'Lowry', 'Lucius', 'Lucas', 'Luke', 'Lukie', 'Lush', 'Luther', 'Lyle', 'Lyndon', 'Lysander',
			'Macnair', 'Maddox', 'Madison', 'Magnus', 'Malcolm', 'Malvin', 'Manfred', 'Mannie', 'Manny', 'Manuel',
			'Marcus', 'Mario', 'Marius', 'Marc', 'Mark', 'Markie', 'Marland', 'Marlon', 'Marmaduke', 'Marmy',
			'Marshal', 'Mart', 'Martie', 'Martin', 'Marty', 'Marv', 'Marvin', 'Mason', 'Matt', 'Matthew',
			'Mattie', 'Matty', 'Maurice', 'Maurie', 'Maury', 'Maxey', 'Maxie', 'Maximilian', 'Maximus', 'Maxy',
			'Maxwell', 'Medwin', 'Melville', 'Melvin', 'Melvyn', 'Mercer', 'Meredith', 'Merlin', 'Merrill', 'Merrick',
			'Merv', 'Mervyn', 'Michael', 'Mick', 'Mickey', 'Micky', 'Mike', 'Mikey', 'Milburn', 'Miles',
			'Miller', 'Milt', 'Miltie', 'Milton', 'Mitch', 'Mitchell', 'Mogga', 'Monroe', 'Montague', 'Monte',
			'Montgomery', 'Monty', 'Mordecai', 'Mordy', 'Morgan', 'Morrey', 'Morrie', 'Morris', 'Morry', 'Mort',
			'Mortimer', 'Morton', 'Morty', 'Morven', 'Mose', 'Moses', 'Moss', 'Murdock', 'Murphy', 'Murray',
			'Myer', 'Naldo', 'Nandy', 'Nanty', 'Nardie', 'Nash', 'Nate', 'Nath', 'Nathan', 'Nathaniel',
			'Nathe', 'Neal', 'Neddie', 'Neddy', 'Nelson', 'Nero', 'Neville', 'Nevin', 'Newton', 'Nicco',
			'Nichol', 'Nicholas', 'Nick', 'Nickie', 'Nicky', 'Nicodemus', 'Nicol', 'Nicolas', 'Nigel', 'Noah',
			'Noddy', 'Noel', 'Noeline', 'Nolan', 'Noll', 'Nolly', 'Norm', 'Norman', 'Normie', 'Norris',
			'Norton', 'Norvin', 'Olly', 'Omar', 'Oren', 'Orland', 'Orlando', 'Orson', 'Orton', 'Orvin',
			'Osbert', 'Osborn', 'Oscar', 'Osgood', 'Osmond', 'Ossie', 'Ossy', 'Oswald', 'Oswold', 'Otis',
			'Otto', 'Owen', 'Oxford', 'Ozzie', 'Ozzy', 'Paine', 'Palmer', 'Parker', 'Pascal', 'Patrick',
			'Paul', 'Paxton', 'Pedro', 'Pembroke', 'Perce', 'Percival', 'Percy', 'Perry', 'Pete', 'Peter',
			'Peyton', 'Phil', 'Philander', 'Philbert', 'Philip', 'Phineas', 'Pierce', 'Pierre', 'Poldie', 'Porter',
			'Prentice', 'Prescott', 'Preston', 'Price', 'Prince', 'Proctor', 'Prosper', 'Putnam', 'Ralph', 'Ramon',
			'Ramsey', 'Rand', 'Randal', 'Randall', 'Randie', 'Randolph', 'Randy', 'Raphael', 'Rasmus', 'Rastus',
			'Raymond', 'Raymund', 'Redmond', 'Reed', 'Reeves', 'Reggie', 'Reginald', 'Renfred', 'Renwick', 'Reub',
			'Reuben', 'Rhett', 'Riccardo', 'Rich', 'Richard', 'Richie', 'Rick', 'Rickie', 'Ricky', 'Ridley',
			'Riley', 'Ritchie', 'Robbie', 'Robert', 'Robin', 'Roddy', 'Roderic', 'Roderick', 'Rodge', 'Rodney',
			'Rodolfo', 'Rodolphe', 'Roger', 'Roland', 'Rolf', 'Rolfe', 'Rolly', 'Roly', 'Romeo', 'Ronald',
			'Ronnie', 'Ronny', 'Rory', 'Roscoe', 'Rosie', 'Ross', 'Roswell', 'Rowland', 'Rowly', 'Royce',
			'Rube', 'Ruby', 'Rudolf', 'Rudolph', 'Rudy', 'Ruford', 'Rufus', 'Rupert', 'Russell', 'Rutherford',
			'Ryan', 'Salisbury', 'Sammy', 'Samson', 'Samuel', 'Sanborn', 'Sander', 'Sanders', 'Sandie', 'Sandy',
			'Sanford', 'Santos', 'Sargent', 'Saul', 'Sawney', 'Sawnie', 'Sawny', 'Sawyer', 'Schuyler', 'Scot',
			'Scott', 'Scotty', 'Sean', 'Sebastian', 'Selby', 'Serge', 'Serle', 'Seth', 'Seymour', 'Shamus',
			'Shaw', 'Shawn', 'Shelby', 'Sheldon', 'Sherard', 'Sheridan', 'Sherlock', 'Sherman', 'Sherwin', 'Sherwood',
			'Siddy', 'Sidney', 'Sigmund', 'Silas', 'Silvain', 'Silvan', 'Silvanus', 'Silvester', 'Silvio', 'Simeon',
			'Simie', 'Simmie', 'Simmy', 'Simon', 'Sinclair', 'Sloane', 'Solly', 'Solomon', 'Spencer', 'Spike',
			'Stacey', 'Stacy', 'Stan', 'Standish', 'Stanford', 'Stanislaus', 'Stan', 'Stanley', 'Stanway', 'Stedman',
			'Steenie', 'Stefan', 'Stephen', 'Sterling', 'Steve', 'Stevie', 'Stew', 'Stewart', 'Stewy', 'Stuart',
			'Sumner', 'Sutton', 'Swain', 'Sylvester', 'Tanner', 'Tate', 'Tave', 'Tavy', 'Taylor', 'Teague',
			'Teddie', 'Teddy', 'Terence', 'Terrence', 'Terry', 'Thad', 'Thadeus', 'Thaddeus', 'Thaddy', 'Thady',
			'Thayer', 'Theo', 'Theobald', 'Theobold', 'Theodore', 'Theodoric', 'Theodosius', 'Theophilus', 'Theron', 'Thomas',
			'Thorpe', 'Thurston', 'Tilden', 'Timmie', 'Timmy', 'Timothy', 'Titus', 'Tobias', 'Toby', 'Todd',
			'Tollie', 'Tolly', 'Tommy', 'Tonie', 'Tony', 'Tony', 'Torrence', 'Torrey', 'Townsend', 'Travers',
			'Travis', 'Trent', 'Trev', 'Trevor', 'Trey', 'Tris', 'Tristam', 'Tristan', 'Tristram', 'Troy',
			'Truman', 'Tyler', 'Tyson', 'Vail', 'Valdis', 'Vance', 'Vandyke', 'Varian', 'Varney', 'Vaughan',
			'Vaughn', 'Vaughnie', 'Vern', 'Vernon', 'Vessie', 'Vest', 'Vester', 'Vick', 'Victor', 'Vince',
			'Vincent', 'Vinnie', 'Vinny', 'Virgil', 'Vito', 'Vladimir', 'Wade', 'Waggoner', 'Walden', 'Waldo',
			'Walker', 'Wallace', 'Wally', 'Walt', 'Walter', 'Walton', 'Ward', 'Ware', 'Warner', 'Warren',
			'Washington', 'Watt', 'Watty', 'Waylen', 'Wayland', 'Wayne', 'Webb', 'Webster', 'Wendel', 'Wesley',
			'Weston', 'Whitlaw', 'Wilbur', 'Wilf', 'Will', 'Willard', 'William', 'Willie', 'Willis', 'Willy',
			'Wilmer', 'Wilmot', 'Wilson', 'Winfred', 'Winslow', 'Winston', 'Winthrop', 'Wirt', 'Wolfe', 'Wolfram',
			'Woodley', 'Woodrow', 'Woodward', 'Worthington', 'Wright', 'Wyatt', 'Wylie', 'Wyman', 'Wyndham');

		static protected $LastNameArray = array('Ho', 'Giffin', 'Smith', 'Johnson', 'Williams', 'Jones', 'Brown',
			'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris',
			'Martin', 'Thompson', 'Garcia', 'Martinez', 'Robinson', 'Clark', 'Rodriguez', 'Lewis', 'Lee', 'Walker',
			'Hall', 'Allen', 'Young', 'Hernandez', 'King', 'Wright', 'Lopez', 'Hill', 'Scott', 'Green',
			'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter', 'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips',
			'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins', 'Stewart', 'Sanchez', 'Morris', 'Rogers', 'Reed',
			'Cook', 'Morgan', 'Bell', 'Murphy', 'Bailey', 'Rivera', 'Cooper', 'Richardson', 'Cox', 'Howard',
			'Ward', 'Torres', 'Peterson', 'Gray', 'Ramirez', 'James', 'Watson', 'Brooks', 'Kelly', 'Sanders',
			'Price', 'Bennett', 'Wood', 'Barnes', 'Ross', 'Henderson', 'Coleman', 'Jenkins', 'Perry', 'Powell',
			'Long', 'Patterson', 'Hughes', 'Flores', 'Washington', 'Butler', 'Simmons', 'Foster', 'Gonzales', 'Bryant',
			'Alexander', 'Russell', 'Griffin', 'Diaz', 'Hayes', 'Chang', 'Chan', 'Hwang', 'Tsai', 'Shaw',
			'Lee', 'Lin', 'Ling', 'Liu', 'Kim', 'Murphy', 'Kelly', 'O\'Sullivan', 'Walsh', 'O\'Brien',
			'Byrne', 'Ryan', 'O\'Connor', 'O\'Neill', 'O\'Reilly', 'Doyle', 'McCarthy', 'Gallagher', 'Doherty', 'Kennedy',
			'Lynch', 'Murray', 'Quinn', 'Moore', 'McLaughlin', 'O\'Carroll', 'Connolly', 'Daly', 'O\'Connell', 'Wilson',
			'Dunne', 'Brennan', 'Burke', 'Collins', 'Campbell', 'Clarke', 'Johnston', 'Hughes', 'O\'Farrell', 'Fitzgerald',
			'Browne', 'Martin', 'Maguire', 'Nolan', 'Flynn', 'Thompson', 'O\'Callaghan', 'O\'Donnell', 'Duffy', 'Mahoney',
			'Boyle', 'Healy', 'O\'Shea', 'White', 'Sweeney', 'Hayes', 'Kavanagh', 'Power', 'McGrath', 'Moran',
			'Brady', 'Stewart', 'Casey', 'Foley', 'Fitzpatrick', 'O\'Leary', 'McDonnell', 'McMahon', 'Donnelly', 'Regan',
			'O\'Donovan', 'Burns', 'Flanagan', 'Mullan', 'Barry', 'Kane', 'Robinson', 'Cunningham', 'Griffin', 'Kenney',
			'Sheehan', 'Ward', 'Whelan', 'Lyons', 'Reid', 'Graham', 'Higgins', 'Cullen', 'Keane', 'King',
			'Maher', 'McKenna', 'Bell', 'Scott', 'Hogan', 'O\'Keeffe', 'Magee', 'McNamara', 'McDonald', 'McDermott',
			'Moloney', 'O\'Rourke', 'Buckley', 'Dwyer');

		static protected $WordArray = array('wheel', 'island', 'turtle', 'chair', 'ear', 'shoe', 'basketball',
			'octopus', 'bed', 'flag', 'castle', 'paint', 'car', 'horse', 'pinwheel', 'kite', 'safetypin',
			'submarine', 'watermelon', 'tea', 'telephone', 'whistle', 'piano', 'clam', 'ring', 'frog', 'olive',
			'mailman', 'mountain', 'camel', 'wind', 'summer', 'green', 'surfboard', 'cow', 'pencil', 'shower',
			'glasses', 'stove', 'chimney', 'window', 'rainbow', 'moon', 'peacock', 'sky', 'ocean', 'volcano',
			'dinosaur', 'whale', 'elephant', 'flea', 'snail', 'fireplace', 'forest', 'spoon', 'lace', 'gasoline',
			'rice', 'honeybee', 'shoulderpad', 'arm', 'ask', 'axe', 'bat', 'big', 'bow', 'box',
			'cat', 'cup', 'dog', 'eat', 'egg', 'eye', 'fan', 'fin', 'fly', 'gap',
			'gun', 'hat', 'jug', 'key', 'lip', 'mug', 'odd', 'jar', 'jet', 'peg',
			'pen', 'pig', 'pin', 'sad', 'saw', 'see', 'sun', 'ton', 'van', 'bald',
			'beak', 'bell', 'belt', 'bite', 'blow', 'bolt', 'bomb', 'bone', 'book', 'boot',
			'cane', 'card', 'chin', 'clam', 'cork', 'crab', 'cube', 'dart', 'deep', 'dice',
			'door', 'down', 'drip', 'duck', 'edge', 'face', 'fang', 'fish', 'fist', 'flag',
			'fold', 'fork', 'full', 'golf', 'gong', 'grin', 'heel', 'hook', 'idea', 'inch',
			'iron', 'jump', 'kick', 'kite', 'knee', 'lava', 'left', 'lens', 'loop', 'mail',
			'male', 'mast', 'maze', 'moon', 'nail', 'neck', 'nose', 'oval', 'palm', 'path',
			'pipe', 'plug', 'roof', 'root', 'rope', 'safe', 'sail', 'scar', 'ship', 'shin',
			'sign', 'sing', 'skis', 'sock', 'sofa', 'spot', 'stop', 'swan', 'talk', 'tall',
			'tent', 'thin', 'tree', 'well', 'west', 'wide', 'wing', 'wink', 'wolf', 'angle',
			'ankle', 'anvil', 'apple', 'arrow', 'beard', 'blank', 'broom', 'cabin', 'chain', 'chair',
			'cheek', 'clown', 'colon', 'crack', 'cross', 'crown', 'dream', 'dress', 'drink', 'eagle',
			'elbow', 'empty', 'fence', 'ghost', 'globe', 'happy', 'heart', 'house', 'igloo', 'japan',
			'joker', 'knife', 'label', 'lapel', 'large', 'laugh', 'medal', 'money', 'mouth', 'music',
			'noose', 'north', 'panda', 'patch', 'peace', 'petal', 'piano', 'pivot', 'point', 'pound',
			'prism', 'punch', 'quack', 'rifle', 'right', 'robot', 'round', 'ruler', 'scale', 'shark',
			'shout', 'skull', 'small', 'smell', 'smile', 'smoke', 'snail', 'south', 'spoon', 'spray',
			'straw', 'sword', 'tepee', 'thick', 'think', 'thumb', 'trunk', 'twins', 'unzip', 'whale',
			'wheel', 'witch', 'anchor', 'apache', 'banana', 'barrel', 'bottle', 'buckle', 'bullet', 'cactus',
			'camera', 'candle', 'cannon', 'castle', 'center', 'cheese', 'church', 'collar', 'comedy', 'convex',
			'cookie', 'corner', 'divide', 'dollar', 'eraser', 'eskimo', 'faucet', 'female', 'finger', 'flower',
			'funnel', 'grapes', 'hammer', 'helmet', 'icicle', 'kitten', 'ladder', 'launch', 'listen', 'locket',
			'magnet', 'medium', 'middle', 'muscle', 'oneway', 'pencil', 'period', 'pillar', 'pirate', 'planet',
			'poison', 'profit', 'puzzle', 'rabbit', 'record', 'rocket', 'saddle', 'salute', 'shield', 'shovel',
			'shower', 'sickle', 'square', 'stairs', 'stereo', 'stilts', 'stripe', 'switch', 'target', 'tennis',
			'toilet', 'tongue', 'trophy', 'turtle', 'zipper', 'antenna', 'balloon', 'barbell', 'bigfoot', 'chimney',
			'concave', 'cupcake', 'cyclops', 'earmuff', 'earring', 'eyeball', 'feather', 'gondola', 'grenade',
			'iceberg', 'keyhole', 'keyring', 'knuckle', 'ladybug', 'lighter', 'mailbox', 'missile', 'monster',
			'necktie', 'overlap', 'padlock', 'pelican', 'percent', 'pulltab', 'pumpkin', 'pyramid', 'quarter',
			'sausage', 'shallow', 'sheriff', 'snorkel', 'snowman', 'stinger', 'vampire', 'volcano', 'whisper',
			'whistle', 'cassette', 'cemetery', 'chainsaw', 'cufflink', 'diagonal', 'diameter', 'dinosaur',
			'doghouse', 'dominoes', 'doorbell', 'doorstep', 'dynamite', 'elephant', 'elevator', 'envelope',
			'flagpole', 'football', 'goldfish', 'handball', 'headband', 'infinity', 'lollipop', 'mountain',
			'multiply', 'mushroom', 'necklace', 'negative', 'overbite', 'positive', 'postcard', 'question',
			'ricochet', 'scissors', 'shoelace', 'shoulder', 'starfish', 'suitcase', 'surround', 'teardrop',
			'timebomb', 'triplets', 'trombone', 'umbrella', 'unicycle', 'upstairs', 'vertical', 'windmill',
			'blockhead', 'boomerang', 'bumblebee', 'butterfly', 'checkmark', 'cigarette', 'different', 'direction',
			'hamburger', 'honeycomb', 'horseshoe', 'hourglass', 'lampshade', 'launchpad', 'lightbulb', 'megaphone',
			'northeast', 'northstar', 'northwest', 'parachute', 'periscope', 'rectangle', 'scarecrow', 'sideburns',
			'southeast', 'southwest', 'stoplight', 'submarine', 'swordfish', 'telephone', 'binoculars', 'cloverleaf',
			'downstairs', 'drawbridge', 'fingernail', 'flashlight', 'footprints', 'helicopter', 'horizontal',
			'paintbrush', 'skateboard', 'sunglasses', 'tablespoon', 'toothbrush', 'caterpillar', 'loudspeaker',
			'screwdriver', 'thermometer', 'wheelbarrow', 'cheeseburger', 'perpendicular', 't shirt', 'yo yo',
			'big ben', 'big hand', 'bow tie', 'cue ball', 'eye patch', 'fig leaf', 'fly swatter', 'for sale',
			'ice skate', 'peg leg', 'six oclock', 'ski jump', 'tea kettle', 'top hat', 'coat hanger', 'dead bolt',
			'fire hydrant', 'four eyes', 'half hour', 'knee pad', 'left hand', 'ping pong', 'punk rocker', 'shoe box',
			'slot machine', 'tape measure', 'wine glass', 'brick wall', 'bulls eye', 'candy cane', 'cross eyed',
			'eight ball', 'first floor', 'great britain', 'laser beam', 'morse code', 'north pole', 'paint can',
			'paper clip', 'right hand', 'short juice', 'south pole', 'stick figure', 'teddy bear', 'third floor',
			'wrist watch', 'bottle cap', 'broken heart', 'bubble gum', 'credit card', 'floppy disk', 'little hand',
			'rabbit ears', 'roller skate', 'saftey pin', 'second floor', 'spider web', 'spiked heel', 'square inch',
			'toilet paper', 'tuning fork', 'upside down', 'diamond ring', 'eyebrow pencil', 'fishing pole',
			'picture frame', 'electric guitar', 'icecream cone', 'shooting star', 'christmas tree', 'lightning rod',
			'quotation marks', 'telephone pole', 'hot air balloon', 'tic tac toe', 'two left feet', 'ball and chain');

		static protected $LipsumArray = array(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			'Donec egestas malesuada nisi.',
			'Pellentesque in mauris.',
			'Sed libero lorem, facilisis ac, suscipit a, convallis vitae, nunc.',
			'Nulla vitae diam.',
			'Quisque lorem.',
			'Sed fringilla leo tincidunt lectus.',
			'Aenean iaculis, tellus eget fringilla pulvinar, orci enim ultrices ipsum, ac dignissim leo nisl vitae mauris.',
			'Duis vitae sem.',
			'Proin feugiat ante ut sem.',
			'Curabitur in neque.',
			'Donec et augue.',
			'Aliquam ac nibh a lacus iaculis malesuada.',
			'Integer luctus suscipit nisl.',
			'Nulla condimentum luctus tortor.',
			'Nunc ultrices.',
			'Suspendisse dapibus fringilla lectus.',
			'Maecenas at sem et dolor rutrum molestie.',
			'In congue scelerisque magna.',
			'In ultrices porta mauris.',
			'Fusce mauris.',
			'Cras sit amet tortor vitae purus ultricies feugiat.',
			'Curabitur et leo ut nisi condimentum accumsan.',
			'Nulla nisl magna, sodales et, tincidunt sit amet, egestas a, ipsum.',
			'Curabitur nisi.',
			'Nam auctor blandit nisl.',
			'Vestibulum arcu purus, sollicitudin hendrerit, pharetra in, accumsan ac, ipsum.',
			'Donec nulla lectus, consequat ac, ornare id, tempus quis, ipsum.',
			'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
			'Morbi mattis euismod mauris.',
			'Mauris pulvinar.',
			'Vestibulum lacus lectus, rhoncus eget, lobortis gravida, interdum eu, ligula.',
			'In hac habitasse platea dictumst.',
			'Vivamus facilisis sodales mauris.',
			'Quisque auctor ligula non purus.',
			'Cras eu libero.',
			'Proin sem neque, adipiscing vel, commodo et, tempus vitae, purus.',
			'Sed lacinia, mi eu laoreet placerat, sapien sem malesuada odio, id volutpat arcu risus a sem.',
			'Morbi pretium lectus eu nisi.',
			'Proin porttitor odio sed sem.',
			'Curabitur adipiscing malesuada velit.',
			'Sed iaculis ligula sit amet nulla.',
			'Fusce ultrices sagittis purus.',
			'Nunc posuere felis id nisi.',
			'Fusce nisi arcu, rutrum a, ultrices ac, convallis quis, nisl.',
			'Sed eu est non lacus auctor dictum.',
			'Nam ultricies tempus ligula.',
			'Vestibulum quis dolor sit amet nisl semper adipiscing.',
			'Etiam sagittis erat non purus.',
			'Fusce tempus tincidunt nulla.',
			'Nunc malesuada magna ac neque.',
			'In hac habitasse platea dictumst.',
			'Aliquam tortor.',
			'Vestibulum tincidunt nisl sit amet nulla.',
			'Quisque at ligula sit amet tellus hendrerit dictum.',
			'Nulla facilisi.',
			'Pellentesque in odio.',
			'Maecenas consectetur.',
			'Maecenas gravida tincidunt sem.',
			'In hac habitasse platea dictumst.',
			'Ut nec diam sed diam rutrum feugiat.',
			'Sed egestas.',
			'Nunc vestibulum.',
			'Phasellus accumsan.',
			'Maecenas aliquam diam lobortis orci.',
			'Curabitur id metus.',
			'Nullam commodo arcu sit amet turpis.',
			'Vivamus a felis.',
			'Sed tristique egestas quam.',
			'Nunc nec orci.',
			'Proin pretium blandit velit.',
			'Nam quis est.',
			'Donec at arcu.',
			'Donec vestibulum.',
			'In ac purus.',
			'Sed dignissim.',
			'Morbi pharetra.',
			'Fusce iaculis, urna sit amet hendrerit imperdiet, erat nulla placerat metus, ut pretium sapien lectus sed urna.',
			'Maecenas egestas, mauris sit amet suscipit pretium, massa lacus facilisis eros, eu sodales dui eros eu nisl.',
			'Nunc ante nisl, fringilla id, rhoncus ut, laoreet at, orci.',
			'Mauris vulputate purus et purus.',
			'Sed malesuada sapien a orci.',
			'Sed semper est eu diam.',
			'Praesent sem nisi, malesuada et, blandit quis, consectetur a, nisl.',
			'In euismod massa condimentum nisi.',
			'Integer vel purus vel mauris dapibus elementum.',
			'Ut euismod, urna ac rhoncus hendrerit, erat metus blandit nunc, vel molestie felis massa eget turpis.',
			'Vestibulum et est in nibh porttitor interdum.',
			'Suspendisse vitae sapien.',
			'Nulla iaculis, tortor non tristique tempus, arcu tellus fermentum quam, quis elementum massa mi vel nibh.',
			'Aliquam erat volutpat.',
			'Morbi quis metus.',
			'Nulla posuere.',
			'Suspendisse aliquet pharetra enim.',
			'Morbi sagittis.',
			'Morbi imperdiet nunc a tortor.',
			'Sed hendrerit, tellus vel mattis malesuada, leo nunc feugiat dui, nec interdum massa augue porta risus.',
			'Praesent a dui non velit eleifend euismod.',
			'Maecenas consequat tristique elit.',
			'Curabitur in justo sed nibh vestibulum pharetra.',
			'Aliquam ac orci.',
			'Vestibulum commodo interdum purus.',
			'Vivamus suscipit tellus nec metus.',
			'Donec pulvinar aliquam justo.',
			'Donec ante.',
			'Curabitur nec elit at justo mollis sollicitudin.',
			'Sed ipsum.',
			'Phasellus augue.',
			'Nulla accumsan velit et nisl.',
			'Sed varius.',
			'Suspendisse viverra semper magna.',
			'Sed suscipit risus sit amet odio.',
			'Nullam ac odio nec enim ornare lacinia.',
			'Nullam at arcu.',
			'Nunc risus purus, posuere at, hendrerit et, porttitor id, tellus.',
			'Nam pretium neque et eros luctus tincidunt.',
			'Nam laoreet justo.',
			'Donec facilisis.',
			'Nunc dapibus, elit ut ultricies ultricies, nulla magna pharetra est, et tempus lacus velit id orci.',
			'Integer ut libero.',
			'Suspendisse non elit.',
			'Integer eros ipsum, suscipit id, feugiat a, tempor a, nunc.',
			'Aenean neque tortor, scelerisque sed, pretium in, tristique iaculis, mauris.',
			'Donec hendrerit eros vitae est.',
			'Curabitur nibh.',
			'In varius, arcu vitae placerat malesuada, elit est semper lectus, eu facilisis ante elit sit amet arcu.',
			'Nulla facilisi.',
			'Quisque iaculis turpis.',
			'Fusce porta orci eu lorem.',
			'Mauris tortor.',
			'Mauris interdum ligula nec tortor.',
			'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
			'Nullam tincidunt.',
			'Nulla lacus odio, hendrerit id, volutpat non, auctor in, leo.',
			'Sed sollicitudin, quam ut pulvinar posuere, ipsum nibh faucibus nibh, quis pulvinar lectus elit vitae diam.',
			'Proin ut nibh.',
			'Sed vestibulum, odio eget lacinia hendrerit, sem elit faucibus sem, at accumsan dolor ligula sit amet felis.',
			'Etiam tincidunt.',
			'Integer pellentesque lacus ut nibh.',
			'Praesent interdum, turpis quis faucibus sagittis, neque nulla porta erat, vitae auctor neque nisl vitae ligula.',
			'Nulla aliquet tincidunt risus.',
			'Fusce cursus tempor velit.',
			'Aenean blandit.',
			'Morbi nisl.',
			'Ut convallis leo luctus nibh.',
			'Curabitur convallis, felis id scelerisque egestas, nunc turpis eleifend mauris, eget placerat leo nulla id sem.',
			'Aliquam bibendum, erat et mollis tincidunt, velit magna facilisis nisi, pharetra venenatis sem libero non nibh.',
			'Nullam elementum iaculis leo.',
			'Aliquam a ipsum quis lectus elementum rutrum.',
			'Suspendisse potenti.',
			'Praesent cursus porta orci.',
			'Suspendisse potenti.',
			'Mauris lectus.',
			'Vestibulum ut urna.',
			'Mauris dapibus luctus mi.',
			'Suspendisse faucibus eleifend diam.',
			'Ut lacinia neque eu nulla.',
			'Aliquam et mi.',
			'Nullam nec urna ac mi hendrerit tempus.',
			'Curabitur quis metus.',
			'In hac habitasse platea dictumst.',
			'Morbi pulvinar tempor nunc.',
			'Maecenas adipiscing massa sed quam.',
			'Integer dictum magna a tellus.',
			'In orci.',
			'Nulla sapien risus, lobortis vitae, posuere in, dictum nec, mi.',
			'Etiam dignissim neque porta enim scelerisque egestas.',
			'Sed nec massa.',
			'Duis facilisis, ante non vehicula venenatis, risus lacus sagittis arcu, ut pretium turpis nunc ut tellus.',
			'Aliquam erat volutpat.',
			'Integer placerat diam quis metus.',
			'Suspendisse potenti.',
			'Ut et risus quis justo iaculis vestibulum.',
			'Phasellus sed leo a massa sodales scelerisque.',
			'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
			'Phasellus quis urna quis turpis tempor porta.',
			'Vestibulum mollis, sapien et varius hendrerit, nisl quam hendrerit massa, vel feugiat dui erat ac arcu.',
			'Curabitur consectetur, libero eget mattis feugiat, tortor lorem rhoncus purus, nec porta augue lectus vel ligula.',
			'Proin posuere rhoncus ligula.',
			'Ut sapien orci, tristique id, rhoncus ac, euismod sed, diam.',
			'Etiam nibh urna, placerat ut, pulvinar gravida, rhoncus id, nibh.',
			'Morbi consequat enim sed metus.',
			'Nunc consectetur lectus ut erat.',
			'Fusce metus lectus, ultricies non, fringilla quis, vulputate euismod, lorem.',
			'Proin pretium cursus ipsum.',
			'Nulla volutpat, turpis vel iaculis interdum, tortor eros molestie massa, id placerat massa magna vel magna.',
			'Praesent in ipsum eu massa tempus euismod.',
			'Mauris consequat.',
			'Aenean venenatis felis vitae leo.',
			'In faucibus fringilla ante.',
			'Etiam leo sapien, varius vestibulum, tincidunt nec, laoreet eu, lectus.',
			'Integer consectetur ipsum fringilla nunc.',
			'Proin tincidunt pellentesque urna.',
			'Phasellus mollis, orci id interdum condimentum, sapien nunc aliquam est, nec tristique odio mi vitae nibh.',
			'Aliquam erat volutpat.',
			'Aenean purus.',
			'Fusce sodales pretium augue.',
			'Proin tempor.',
			'Mauris tristique lacinia tellus.',
			'Sed tincidunt arcu eu quam.',
			'Proin placerat, nulla sit amet sollicitudin rutrum, metus ipsum facilisis felis, eget dapibus ante elit nec nulla.',
			'Morbi gravida pretium ante.',
			'Nullam faucibus, urna vel porttitor sagittis, sapien diam tempor diam, ac tempor ipsum diam sit amet enim.',
			'In porta rhoncus libero.',
			'Suspendisse potenti.',
			'Proin nec arcu eu ipsum sollicitudin hendrerit.',
			'Suspendisse ullamcorper ligula sed risus.',
			'Vivamus eget arcu in odio aliquam viverra.',
			'Morbi vulputate tincidunt quam.',
			'Suspendisse potenti.',
			'Suspendisse aliquam risus vel neque.',
			'Praesent fermentum suscipit eros.',
			'Duis sit amet mauris.',
			'Quisque at odio.',
			'Sed nec augue et turpis tincidunt accumsan.',
			'Pellentesque eu sem.',
			'Praesent tellus metus, molestie non, tristique facilisis, rutrum ut, odio.',
			'Nulla facilisis neque nec est.',
			'Ut nunc nulla, dapibus sit amet, sollicitudin id, posuere eu, libero.',
			'Curabitur elit.',
			'Duis sapien ante, vulputate at, dapibus a, molestie eu, eros.',
			'Nulla fringilla ipsum quis justo.',
			'Suspendisse semper.',
			'Aenean ac neque.',
			'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
			'Nam imperdiet.',
			'Phasellus varius dolor.',
			'Vivamus iaculis tincidunt lacus.',
			'Duis turpis tellus, pulvinar eu, interdum facilisis, euismod at, tellus.',
			'Ut lacus.',
			'Proin condimentum, neque quis rhoncus tempus, nunc risus imperdiet velit, non semper mauris dui ut dolor.',
			'Donec convallis leo ut nunc.',
			'Vestibulum vitae massa.',
			'Integer mollis.',
			'Nullam elementum.',
			'Fusce pellentesque leo eu sapien.',
			'Aliquam nunc.',
			'Maecenas at diam.',
			'Proin venenatis nisl nec eros.',
			'Suspendisse potenti.',
			'Suspendisse potenti.',
			'Suspendisse condimentum.',
			'Suspendisse et ante eu ante pulvinar ullamcorper.',
			'Suspendisse potenti.',
			'Vestibulum ut neque sed ante suscipit porttitor.',
			'Nulla facilisi.',
			'Proin odio.',
			'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
			'Vivamus eget magna.',
			'Suspendisse dignissim lacus non ipsum.',
			'Phasellus in tortor.',
			'Vivamus justo.',
			'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce fringilla.',
			'Donec vel orci.',
			'Aenean elementum rutrum tellus.',
			'Donec eleifend.',
			'Sed nec sapien.',
			'Suspendisse risus lacus, dictum eget, interdum sodales, pellentesque quis, risus.',
			'Ut vitae turpis.',
			'In lacus lectus, aliquet in, blandit eget, tristique id, enim.',
			'Maecenas sed urna.',
			'Morbi ullamcorper est sit amet nisi.',
			'Aliquam erat volutpat.',
			'Praesent non lorem nec eros dignissim fringilla.',
			'Nullam rhoncus ligula et ligula.',
			'Aenean non odio.',
			'Mauris enim.',
			'Ut elementum dapibus lacus.',
			'Nulla venenatis metus ut neque.',
			'Aliquam ultricies accumsan urna.',
			'Aliquam vitae libero.',
			'Nullam enim leo, scelerisque accumsan, sagittis vel, pellentesque et, ipsum.',
			'Ut eu orci vitae elit mollis eleifend.',
			'Proin tristique, purus iaculis ultrices pharetra, odio tellus sagittis sem, eget faucibus nunc libero non nibh.',
			'Donec rutrum, sapien ornare lobortis fringilla, magna odio rhoncus mauris, quis rhoncus sapien erat et est.',
			'Morbi a turpis non arcu auctor fermentum.',
			'Suspendisse pharetra.',
			'Praesent ligula.',
			'Vivamus tristique, magna sed posuere ullamcorper, odio mi scelerisque massa, ut dapibus odio lacus tincidunt diam.',
			'Maecenas pharetra libero quis tortor.',
			'Quisque erat nunc, lacinia quis, dapibus eget, dignissim sed, lorem.',
			'Pellentesque vitae arcu.',
			'Nulla facilisi.',
			'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
			'In hac habitasse platea dictumst.',
			'Aenean suscipit metus at nunc fringilla tempor.',
			'Aenean eget magna eget massa congue egestas.',
			'In gravida leo vel felis.',
			'Suspendisse potenti.',
			'Vestibulum tristique condimentum justo.',
			'Proin nec nunc in magna placerat tincidunt.',
			'In eget felis quis dui blandit scelerisque.',
			'Ut sapien eros, gravida nec, placerat et, luctus eu, libero.',
			'Quisque eros elit, laoreet quis, consectetur ac, posuere ac, velit.',
			'Vestibulum a lectus.',
			'Suspendisse pretium, nisl nec facilisis ullamcorper, dui arcu eleifend urna, quis vulputate nibh enim eget magna.',
			'Nam id enim.');

		static protected $EmailDomainArray = array('hotmail.com', 'mail.nasa.gov', 'gmail.com', 'yahoo.com', 'rocketmail.com',
			'lycos.com', 'sbcglobal.net', 'sbcyahoo.com', 'earthlink.net', 'attmail.com', 'aol.com', 'excite.com',
			'northwestern.edu', 'rice.edu', 'freemail.com');

		static protected $TldArray = array('com', 'net', 'org', 'co.uk', 'biz', 'info', 'tv', 'co.za');		

		static protected $CityArray = array('Adelanto', 'Agoura Hills', 'Alameda', 'Albany', 'Alhambra', 'Aliso Viejo', 'Alturas',
			'Amador', 'American Canyon', 'Anaheim', 'Anderson', 'Angels Camp', 'Antioch', 'Apple Valley', 'Arcadia', 'Arcata', 'Arroyo Grande',
			'Artesia', 'Arvin', 'Atascadero', 'Atherton', 'Atwater', 'Auburn', 'Avalon', 'Avenal', 'Azusa', 'Bakersfield',
			'Baldwin Park', 'Banning', 'Barstow', 'Beaumont', 'Bell', 'Bell Gardens', 'Bellflower', 'Belmont', 'Belvedere', 'Benicia',
			'Berkeley', 'Beverly Hills', 'Big Bear Lake', 'Biggs', 'Bishop', 'Blue Lake', 'Blythe', 'Bradbury', 'Brawley', 'Brea',
			'Brentwood', 'Brisbane', 'Buellton', 'Buena Park', 'Burbank', 'Burlingame', 'Calabasas', 'Calexico', 'California City', 'Calimesa',
			'Calipatria', 'Calistoga', 'Camarillo', 'Campbell', 'Canyon Lake', 'Capitola', 'Carlsbad', 'Carmel by the Sea', 'Carpinteria', 'Carson',
			'Cathedral City', 'Ceres', 'Cerritos', 'Chico', 'Chino', 'Chino Hills', 'Chowchilla', 'Chula Vista', 'Citrus Heights', 'Claremont',
			'Clayton', 'Clearlake', 'Cloverdale', 'Clovis', 'Coachella', 'Coalinga', 'Colfax', 'Colma', 'Colton', 'Colusa',
			'Commerce', 'Compton', 'Concord', 'Corcoran', 'Corning', 'Corona', 'Coronado', 'Corte Madera', 'Costa Mesa', 'Cotati',
			'Covina', 'Crescent City', 'Cudahy', 'Culver City', 'Cupertino', 'Cypress', 'Daly City', 'Dana Point', 'Danville', 'Davis',
			'Del Mar', 'Del Rey Oaks', 'Delano', 'Desert Hot Springs', 'Diamond Bar', 'Dinuba', 'Dixon', 'Dorris', 'Dos Palos', 'Downey',
			'Duarte', 'Dublin', 'Dunsmuir', 'East Palo Alto', 'El Cajon', 'El Centro', 'El Cerrito', 'El Monte', 'El Segundo', 'Elk Grove',
			'Emeryville', 'Encinitas', 'Escalon', 'Escondido', 'Etna', 'Eureka', 'Exeter', 'Fairfax', 'Fairfield', 'Farmersville',
			'Ferndale', 'Fillmore', 'Firebaugh', 'Folsom', 'Fontana', 'Fort Bragg', 'Fort Jones', 'Fortuna', 'Foster City', 'Fountain Valley',
			'Fowler', 'Fremont', 'Fresno', 'Fullerton', 'Galt', 'Garden Grove', 'Gardena', 'Gilroy', 'Glendale', 'Glendora',
			'Goleta', 'Gonzales', 'Grand Terrace', 'Grass Valley', 'Greenfield', 'Gridley', 'Grover Beach', 'Guadalupe', 'Gustine', 'Half Moon Bay',
			'Hanford', 'Hawaiian Gardens', 'Hawthorne', 'Hayward', 'Healdsburg', 'Hemet', 'Hercules', 'Hermosa Beach', 'Hesperia', 'Hidden Hills',
			'Highland', 'Hillsborough', 'Hollister', 'Holtville', 'Hughson', 'Huntington Beach', 'Huntington Park', 'Huron', 'Imperial', 'Imperial Beach',
			'Indian Wells', 'Indio', 'Industry', 'Inglewood', 'Ione', 'Irvine', 'Irwindale', 'Isleton', 'Jackson', 'Kerman',
			'King City', 'Kingsburg', 'La Canada Flintridge', 'La Habra', 'La Habra Heights', 'La Mesa', 'La Mirada', 'La Palma', 'La Puente', 'La Quinta',
			'La Verne', 'Lafayette', 'Laguna Beach', 'Laguna Hills', 'Laguna Niguel', 'Laguna Woods', 'Lake Elsinore', 'Lake Forest', 'Lakeport', 'Lakewood',
			'Lancaster', 'Larkspur', 'Lathrop', 'Lawndale', 'Lemon Grove', 'Lemoore', 'Lincoln', 'Lindsay', 'Live Oak', 'Livermore',
			'Livingston', 'Lodi', 'Loma Linda', 'Lomita', 'Lompoc', 'Long Beach', 'Loomis', 'Los Alamitos', 'Los Altos', 'Los Altos Hills',
			'Los Angeles', 'Los Banos', 'Los Gatos', 'Loyalton', 'Lynwood', 'Madera', 'Malibu', 'Mammoth Lakes', 'Manhattan Beach', 'Manteca',
			'Maricopa', 'Marina', 'Martinez', 'Marysville', 'Maywood', 'McFarland', 'Mendota', 'Menlo Park', 'Merced', 'Mill Valley',
			'Millbrae', 'Milpitas', 'Mission Viejo', 'Modesto', 'Monrovia', 'Montague', 'Montclair', 'Monte Sereno', 'Montebello', 'Monterey',
			'Monterey Park', 'Moorpark', 'Moraga', 'Moreno Valley', 'Morgan Hill', 'Morro Bay', 'Mountain View', 'Mt. Shasta', 'Murrieta', 'Napa',
			'National City', 'Needles', 'Nevada City', 'Newark', 'Newman', 'Newport Beach', 'Norco', 'Norwalk', 'Novato', 'Oakdale',
			'Oakland', 'Oakley', 'Oceanside', 'Ojai', 'Ontario', 'Orange', 'Orange Cove', 'Orinda', 'Orland', 'Oroville',
			'Oxnard', 'Pacific Grove', 'Pacifica', 'Palm Desert', 'Palm Springs', 'Palmdale', 'Palo Alto', 'Palos Verdes Estates', 'Paradise', 'Paramount',
			'Parlier', 'Pasadena', 'Paso Robles', 'Patterson', 'Perris', 'Petaluma', 'Pico Rivera', 'Piedmont', 'Pinole', 'Pismo Beach',
			'Pittsburg', 'Placentia', 'Placerville', 'Pleasant Hill', 'Pleasanton', 'Plymouth', 'Point Arena', 'Pomona', 'Port Hueneme', 'Porterville',
			'Portola', 'Portola Valley', 'Poway', 'Rancho Cordova', 'Rancho Cucamonga', 'Rancho Mirage', 'Rancho Palos Verdes', 'Rancho Santa Margarita', 'Red Bluff', 'Redding',
			'Redlands', 'Redondo Beach', 'Redwood City', 'Reedley', 'Rialto', 'Richmond', 'Ridgecrest', 'Rio Dell', 'Rio Vista', 'Ripon',
			'Riverbank', 'Riverside', 'Rocklin', 'Rohnert Park', 'Rolling Hills', 'Rolling Hills Estates', 'Rosemead', 'Roseville', 'Ross', 'Sacramento',
			'Salinas', 'San Anselmo', 'San Bernardino', 'San Bruno', 'San Carlos', 'San Clemente', 'San Diego', 'San Dimas', 'San Fernando', 'San Francisco',
			'San Gabriel', 'San Jacinto', 'San Joaquin', 'San Jose', 'San Juan Bautista', 'San Juan Capistrano', 'San Leandro', 'San Luis Obispo', 'San Marcos', 'San Marino',
			'San Mateo', 'San Pablo', 'San Rafael', 'San Ramon', 'Sand City', 'Sanger', 'Santa Ana', 'Santa Barbara', 'Santa Clara', 'Santa Clarita',
			'Santa Cruz', 'Santa Fe Springs', 'Santa Maria', 'Santa Monica', 'Santa Paula', 'Santa Rosa', 'Santee', 'Saratoga', 'Sausalito', 'Scotts Valley',
			'Seal Beach', 'Seaside', 'Sebastopol', 'Selma', 'Shafter', 'Shasta Lake', 'Sierra Madre', 'Signal Hill', 'Simi Valley', 'Solana Beach',
			'Soledad', 'Solvang', 'Sonoma', 'Sonora', 'South El Monte', 'South Gate', 'South Lake Tahoe', 'South Pasadena', 'South San Francisco', 'St. Helena',
			'Stanton', 'Stockton', 'Suisun City', 'Sunnyvale', 'Susanville', 'Sutter Creek', 'Taft', 'Tehachapi', 'Tehama', 'Temecula',
			'Temple City', 'Thousand Oaks', 'Tiburon', 'Torrance', 'Tracy', 'Trinidad', 'Truckee', 'Tulare', 'Tulelake', 'Turlock',
			'Tustin', 'Twentynine Palms', 'Ukiah', 'Union City', 'Upland', 'Vacaville', 'Vallejo', 'Ventura', 'Vernon', 'Victorville',
			'Villa Park', 'Visalia', 'Vista', 'Walnut', 'Walnut Creek', 'Wasco', 'Waterford', 'Watsonville', 'Weed', 'West Covina',
			'West Hollywood', 'West Sacramento', 'Westlake Village', 'Westminster', 'Westmorland', 'Wheatland', 'Whittier', 'Williams', 'Willits', 'Willows',
			'Windsor', 'Winters', 'Woodlake', 'Woodland', 'Woodside', 'Yorba Linda', 'Yountville', 'Yreka', 'Yuba City', 'Yucaipa',
			'Yucca Valley');

		static protected $StreetTypeArray = array('Road', 'Way', 'Blvd.', 'Ave.', 'St.', 'Terrace', 'Dr.');
		static protected $Address2UnitTypeArray = array('Apt.', 'Suite', 'Unit');
		static protected $UsStateArray = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID',
			'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC',
			'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY');
	}
?>