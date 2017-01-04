<?php
/**
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */
namespace Drupal\features_master\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
class PortfolioSearchForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'portfolio_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Disable caching on this form.
    $form_state->setCached(FALSE);

    foreach (['type', 'year'] as $key) {
      $value = $this->getRequest()->get($key);
      if ($value) {
        $form_state->set($key, $value);
      }
    }
    $types = ['all', 'Development', 'Content', 'Design'];
    $form['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => array_combine($types, $types),
      '#required' => FALSE,
      '#default_value' => $form_state->get('type') ? $form_state->get('type') : 'all',
      '#ajax' => [
        'callback' => '::setYearFilterCallback',
        'wrapper' => 'year-wrapper',
      ],
    );

    $form['year_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'year-wrapper'],
    ];

    if ($form_state->get('type')) {
      $form['year_wrapper']['year'] = $this->getYearControl($form_state->get('type'), $form_state->get('year'));
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Retrieves used year numbers.
   *
   * @param bool $typeFilter
   * @return mixed
   */
  public function getYears($typeFilter=FALSE) {
    $query = \Drupal::database()->select('node__field_year', '_year')
      ->fields('_year', ['field_year_value'])
      ->orderBy('_year.field_year_value', 'DESC')
      ->distinct();
    if ($typeFilter) {
      $query->join('node__field_type', '_type', '_year.entity_id = _type.entity_id');
      $query->condition('_type.field_type_value', $typeFilter);
    }
    $years = $query->execute()->fetchCol('_year');
    array_unshift($years, 'all');
    return $years;
  }

  /**
   * Builds the year control.
   *
   * @param bool $typeFilter
   * @param string $default_value
   * @return array
   */
  public function getYearControl($typeFilter=FALSE, $default_value='all') {
    $years = $this->getYears($typeFilter);
    return array(
      '#type' => 'select',
      '#title' => $this->t('Year'),
      '#options' => array_combine($years, $years),
      '#default_value' => empty($default_value) ? 'all' : $default_value
    );
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function setYearFilterCallback(array &$form, FormStateInterface $form_state) {
    $form['year'] = $this->getYearControl();

    return $form['year'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect(
      $this->getRouteMatch()->getRouteName(),
      array_intersect_key(
        $form_state->getValues(),
        array_flip(['type', 'year'])
      )
    );
  }

}
