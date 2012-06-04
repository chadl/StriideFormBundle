<?php
namespace Striide\FormBundle\Manager;
use Symfony\Component\Form\FormFactory;
use Striide\FormBundle\Validator\FormValidatorFactory;
abstract 
class FormManager
{
  protected $form_factory = null;
  public function setFormFactory(FormFactory $factory) 
  {
    $this->form_factory = $factory;
  }
  private $form_validator_factory = null;
  public function setFormValidatorFactory(FormValidatorFactory $factory) 
  {
    $this->form_validator_factory = $factory;
  }
  public function getValidationCollection() 
  {
    $collection = $this->form_validator_factory->getValidatorCollection($this->getFormModel());
    return $collection;
  }
  /**
   * @return \Symfony\Component\Form\Form
   */
  abstract public function getForm();
  /**
   * This function must be implemented, and return an AbstractType
   * @return form entity/model
   */
  abstract public function getFormModel();
}
