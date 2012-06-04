<?php
namespace Striide\FormBundle\Extension;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints\Collection;

class FormValidatorTwigExtension extends \Twig_Extension
{
  private $form_validator_factory = null;
  public function setFormValidatorFactory($service) 
  {
    $this->form_validator_factory = $service;
  }
  /**
   * {@inheritdoc}
   */
  public function getFunctions() 
  {
    return array(
      'js_validation' => new \Twig_Function_Method($this, 'jquery_validate_outputter') ,
      'getCountries' => new \Twig_Function_Method($this, 'getCountries') ,
      'getLanguages' => new \Twig_Function_Method($this, 'getLanguages') ,
      'getLocales' => new \Twig_Function_Method($this, 'getLocales') ,
    );
  }
  public function jquery_validate_outputter($form_css_class, Form $form, Collection $validators) 
  {
    $messages = $this->form_validator_factory->getMessages($form, $validators);
    $rules = $this->form_validator_factory->getRules($form, $validators);
    $output = '

<div class="' . $form_css_class . '-alert alert alert-error" style="display:none;"><strong>Sorry!</strong> There is a rule that prevents us from being able to complete this action.<ul></ul></div>
<script type="text/javascript">
	$(function()
	{
		vertical = $("form.' . $form_css_class . '").hasClass("form-vertical");
		$("form.' . $form_css_class . '").validate({
			messages: ' . json_encode($messages) . ',
			rules: ' . json_encode($rules) . ',

			errorClass: (vertical) ? "help-block" : "help-inline",
			showErrors: function(errorMap, errorList)
			{
				$(".control-group", this.currentForm).removeClass("error");

				i = 0;
				for(var key in errorMap)
				{
					i++;
					field = $("[name=\'" + key + "\']", this.currentForm);
					group = field.closest(".control-group");
					group.addClass("error");

					//item = $("<li>"+"hi"+"</li>");
					//$("ul","div.' . $form_css_class . '-alert").append(item)
				}

				if(i > 0)
				{
					$("div.' . $form_css_class . '-alert").show();
				}
				else
				{
					$("div.' . $form_css_class . '-alert").hide();
				}

				this.defaultShowErrors();
			}
		});
	});
</script>
';
    return $output;
  }
  public function getCountries() 
  {
    return json_encode(\Symfony\Component\Locale\Locale::getCountries());
  }
  public function getLanguages() 
  {
    return json_encode(\Symfony\Component\Locale\Locale::getLanguages());
  }
  public function getLocales() 
  {
    return json_encode(\Symfony\Component\Locale\Locale::getLocales());
  }
  /**
   * Returns the name of the extension.
   *
   * @return string The extension name
   */
  public function getName() 
  {
    return 'FormValidatorTwigExtension';
  }
}
