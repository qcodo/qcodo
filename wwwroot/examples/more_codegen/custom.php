<?php require('../../includes/prepend.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div class="instructions">
		<div class="instruction_title">Implementing Custom Business Logic</div>
		Almost no application can be purely code generated.  As you develop your application, you will likely
		have to implement your own custom business rules and functionality.<br/><br/>
		
		At the object level, these business rules can be implemented in the custom subclasses.  In our
		example, we make up a fictional business rule of <b>GetPrice</b> for our <b>Project</b>.  This <b>GetPrice</b>
		method takes in a "discount percentage" parameter, and uses it to recalculate the budget, incorporating the
		discount and adding 8.25% tax.<br/><br/>

		Note how we can do this within the custom subclass.  Any modifications we make in the custom
		subclass will never be overwritten on subsequent re-generations of the code.
	</div>

<?php
	// Let's define our Project SubClass

	// Note: Typically, this code would be in the includes/data_objects/Project.class.php
	// but the Project.class.php code has been pulled out and put here for demonstration
	// purposes.
	require(__DATAGEN_CLASSES__ . '/ProjectGen.class.php');
	class Project extends ProjectGen {
		const TaxPercentage = .0825;

		public function GetPrice($fltDiscount) {
			$fltPrice = $this->fltBudget;
			$fltPrice = $fltPrice * (1.0 - $fltDiscount);
			$fltPrice = $fltPrice * (1.0 + Project::TaxPercentage);

			return $fltPrice;
		}
	}

	// Let's load a Project object -- let's select the Project with ID #3
	$objProject = Project::Load(3);
?>

	<h3>Load a Project Object and Use the New GetPrice Method</h3>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/>
	Project Budget: $<?php _p($objProject->Budget); ?><br/>
	<b>GetPrice</b> @ 0% Discount: $<?php _p($objProject->GetPrice(0)); ?><br/>
	<b>GetPrice</b> @ 10% Discount: $<?php _p($objProject->GetPrice(.1)); ?><br/>


<?php require('../includes/footer.inc.php'); ?>