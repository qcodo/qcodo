<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Implementing a Customized LoadBy or LoadArrayBy</div>
		(Note: for more information about creating custom queries, please refer to Section 3 of the Examples Site.)<br/><br/>

		With the <b>InstantiateDbResult</b> method that is code generated for you in each
		generated class, it is very simple to create your own custom <b>LoadBy</b> or <b>LoadArrayBy</b>
		method using your own custom SQL.  Simply specify a custom Load query by using <b>Qcodo Query</b> (or by writing your
		own SQL statement and passing the results into <b>InstantiateDbResult</b>).  The code generated logic will take care
		of the rest, transforming your DB result into an array of that object.<br/><br/>

		In our example below, we have a custom load function to get an array of all 
		<b>Project</b> objects where the budget is over a given amount.  We pass this amount
		as a parameter to <b>LoadArrayByBudgetMinimum</b>.
	</div>

<?php
	// Let's define our Project SubClass

	// Note: Typically, this code would be in includes/data_objects/Project.class.php
	// but the Project.class.php code has been pulled out and put here for demonstration
	// purposes.
	require(__DATAGEN_CLASSES__ . '/ProjectGen.class.php');
	class Project extends ProjectGen {
		// Create our Custom Load Method
		// Note that this custom load method is based on the sample LoadArrayBySample that is generated
		// in the Project custom subclass.  Because it utilizes the Qcodo Query mechanism,
		// we can easily take full advantage of any QQ Clauses by taking it in as an optional parameter.
		public static function LoadArrayByBudgetMinimum($fltBudgetMinimum, $objOptionalClauses = null) {
			return Project::QueryArray(
				QQ::GreaterOrEqual(QQN::Project()->Budget, $fltBudgetMinimum),
				$objOptionalClauses
			);
		}
	}
?>



	<h3>Load an Array of Projects Where the Budget >= $8,000</h3>
<?php
	// Let's load all Projects > $10,000 in budget
	$objProjectArray = Project::LoadArrayByBudgetMinimum(8000);
	foreach ($objProjectArray as $objProject)
		_p('&bull; ' . QApplication::HtmlEntities($objProject->Name) . ' (Budget: $' . QApplication::HtmlEntities($objProject->Budget) . ')<br/>', false);
?>

<?php require('../includes/footer.inc.php'); ?>