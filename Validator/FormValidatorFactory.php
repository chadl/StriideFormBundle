<?php
namespace Striide\FormBundle\Validator;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class FormValidatorFactory
{
  protected $metadataFactory = null;
  public function setMetaDataFactory(ClassMetadataFactoryInterface $metadataFactory) 
  {
    $this->metadataFactory = $metadataFactory;
  }
  private $translator = null;
  public function setTranslator(Translator $translator) 
  {
    $this->translator = $translator;
  }
  public function getValidatorCollection($form_type) 
  {
    $constraints = array();
    $metadata = $this->metadataFactory->getClassMetadata(get_class($form_type));
    $keys = $metadata->getConstrainedProperties();
    foreach ($keys as $key) 
    {
      $data = $metadata->getMemberMetadatas($key);
      foreach ($data as $propertyMeta) 
      {
        foreach ($propertyMeta->getConstraints() as $c) 
        {
          $c->message = $this->translator->trans($c->message);
          $constraints[$key][] = $c;
        }
      }
    }
    $collectionConstraint = new Collection($constraints);
    return $collectionConstraint;
  }
  private function addMessage($form_name, $messages, $key, $type, $message) 
  {
    $field_key = sprintf("%s[%s]", $form_name, $key);
    
    if (!is_array($messages)) 
    {
      $messages = array();
    }
    
    if (!isset($messages[$field_key])) 
    {
      $messages[$field_key] = array();
    }
    $messages[$field_key][$type] = $message;
    return $messages;
  }
  /**
   * @param string $form_name the name of the form
   * @param \Symfony\Component\Validator\Constraints\Collection $validationCollection the collection of validators
   * @return mixed array of messages to be used by jquery validation
   */
  public function getMessages(Form $form, Collection $validationCollection) 
  {
    $messages = array();
    foreach ($validationCollection->fields as $field_key => $field_validators) 
    {
      foreach ($field_validators as $field_validator) 
      {
        
        if ($field_validator instanceof \Symfony\Component\Validator\Constraints\Email) 
        {
          $messages = $this->addMessage($form->getName() , $messages, $field_key, 'email', $field_validator->message);
        }
        
        if ($field_validator instanceof \Symfony\Component\Validator\Constraints\NotBlank) 
        {
          $messages = $this->addMessage($form->getName() , $messages, $field_key, 'required', $field_validator->message);
        }
      }
    }
    return $messages;
  }
  private function addRule($form_name, $rules, $key, $type) 
  {
    $field_key = sprintf("%s[%s]", $form_name, $key);
    
    if (!is_array($rules)) 
    {
      $rules = array();
    }
    
    if (!isset($rules[$field_key])) 
    {
      $rules[$field_key] = array();
    }
    $rules[$field_key][$type] = true;
    return $rules;
  }
  /**
   * @param string $form_name the name of the form
   * @param \Symfony\Component\Validator\Constraints\Collection $validationCollection the collection of validators
   * @return mixed array of rules to be used by jquery validation
   */
  public function getRules(Form $form, Collection $validationCollection) 
  {
    $rules = array();
    foreach ($validationCollection->fields as $field_key => $field_validators) 
    {
      foreach ($field_validators as $field_validator) 
      {
        
        if ($field_validator instanceof \Symfony\Component\Validator\Constraints\Email) 
        {
          $rules = $this->addRule($form->getName() , $rules, $field_key, 'email');
        }
        
        if ($field_validator instanceof \Symfony\Component\Validator\Constraints\NotBlank) 
        {
          $rules = $this->addRule($form->getName() , $rules, $field_key, 'required');
        }
      }
    }
    return $rules;
  }
}
