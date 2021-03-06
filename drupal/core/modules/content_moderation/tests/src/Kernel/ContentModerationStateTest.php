<?php

namespace Drupal\Tests\content_moderation\Kernel;

use Drupal\content_moderation\Entity\ContentModerationState;
use Drupal\entity_test\Entity\EntityTestBundle;
use Drupal\entity_test\Entity\EntityTestWithBundle;
use Drupal\KernelTests\KernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\workflows\Entity\Workflow;

/**
 * Tests links between a content entity and a content_moderation_state entity.
 *
 * @group content_moderation
 */
class ContentModerationStateTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_test',
    'node',
    'content_moderation',
    'user',
    'system',
    'language',
    'content_translation',
    'text',
    'workflows',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('node', 'node_access');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test_with_bundle');
    $this->installEntitySchema('content_moderation_state');
    $this->installConfig('content_moderation');
  }

  /**
   * Tests basic monolingual content moderation through the API.
   */
  public function testBasicModeration() {
    $node_type = NodeType::create([
      'type' => 'example',
    ]);
    $node_type->save();

    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'example');
    $workflow->save();

    $node = Node::create([
      'type' => 'example',
      'title' => 'Test title',
    ]);
    $node->save();
    $node = $this->reloadNode($node);
    $this->assertEquals('draft', $node->moderation_state->value);

    $node->moderation_state->value = 'published';
    $node->save();

    $node = $this->reloadNode($node);
    $this->assertEquals('published', $node->moderation_state->value);

    // Change the state without saving the node.
    $content_moderation_state = ContentModerationState::load(1);
    $content_moderation_state->set('moderation_state', 'draft');
    $content_moderation_state->setNewRevision(TRUE);
    $content_moderation_state->save();

    $node = $this->reloadNode($node, 3);
    $this->assertEquals('draft', $node->moderation_state->value);
    $this->assertFalse($node->isPublished());

    // Get the default revision.
    $node = $this->reloadNode($node);
    $this->assertTrue($node->isPublished());
    $this->assertEquals(2, $node->getRevisionId());

    $node->moderation_state->value = 'published';
    $node->save();

    $node = $this->reloadNode($node, 4);
    $this->assertEquals('published', $node->moderation_state->value);

    // Get the default revision.
    $node = $this->reloadNode($node);
    $this->assertTrue($node->isPublished());
    $this->assertEquals(4, $node->getRevisionId());

  }

  /**
   * Tests basic multilingual content moderation through the API.
   */
  public function testMultilingualModeration() {
    // Enable French.
    ConfigurableLanguage::createFromLangcode('fr')->save();
    $node_type = NodeType::create([
      'type' => 'example',
    ]);
    $node_type->save();

    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('node', 'example');
    $workflow->save();

    $english_node = Node::create([
      'type' => 'example',
      'title' => 'Test title',
    ]);
    // Revision 1 (en).
    $english_node
      ->setPublished(FALSE)
      ->save();
    $this->assertEquals('draft', $english_node->moderation_state->value);
    $this->assertFalse($english_node->isPublished());

    // Create a French translation.
    $french_node = $english_node->addTranslation('fr', ['title' => 'French title']);
    $french_node->setPublished(FALSE);
    // Revision 1 (fr).
    $french_node->save();
    $french_node = $this->reloadNode($english_node)->getTranslation('fr');
    $this->assertEquals('draft', $french_node->moderation_state->value);
    $this->assertFalse($french_node->isPublished());

    // Move English node to create another draft.
    $english_node = $this->reloadNode($english_node);
    $english_node->moderation_state->value = 'draft';
    // Revision 2 (en, fr).
    $english_node->save();
    $english_node = $this->reloadNode($english_node);
    $this->assertEquals('draft', $english_node->moderation_state->value);

    // French node should still be in draft.
    $french_node = $this->reloadNode($english_node)->getTranslation('fr');
    $this->assertEquals('draft', $french_node->moderation_state->value);

    // Publish the French node.
    $french_node->moderation_state->value = 'published';
    // Revision 3 (en, fr).
    $french_node->save();
    $french_node = $this->reloadNode($french_node)->getTranslation('fr');
    $this->assertTrue($french_node->isPublished());
    $this->assertEquals('published', $french_node->moderation_state->value);
    $this->assertTrue($french_node->isPublished());
    $english_node = $french_node->getTranslation('en');
    $this->assertEquals('draft', $english_node->moderation_state->value);

    // Publish the English node.
    $english_node->moderation_state->value = 'published';
    // Revision 4 (en, fr).
    $english_node->save();
    $english_node = $this->reloadNode($english_node);
    $this->assertTrue($english_node->isPublished());

    // Move the French node back to draft.
    $french_node = $this->reloadNode($english_node)->getTranslation('fr');
    $this->assertTrue($french_node->isPublished());
    $french_node->moderation_state->value = 'draft';
    // Revision 5 (en, fr).
    $french_node->save();
    $french_node = $this->reloadNode($english_node, 5)->getTranslation('fr');
    $this->assertFalse($french_node->isPublished());
    $this->assertTrue($french_node->getTranslation('en')->isPublished());

    // Republish the French node.
    $french_node->moderation_state->value = 'published';
    // Revision 6 (en, fr).
    $french_node->save();
    $french_node = $this->reloadNode($english_node)->getTranslation('fr');
    $this->assertTrue($french_node->isPublished());

    // Change the EN state without saving the node.
    $content_moderation_state = ContentModerationState::load(1);
    $content_moderation_state->set('moderation_state', 'draft');
    $content_moderation_state->setNewRevision(TRUE);
    // Revision 7 (en, fr).
    $content_moderation_state->save();
    $english_node = $this->reloadNode($french_node, $french_node->getRevisionId() + 1);

    $this->assertEquals('draft', $english_node->moderation_state->value);
    $french_node = $this->reloadNode($english_node)->getTranslation('fr');
    $this->assertEquals('published', $french_node->moderation_state->value);

    // This should unpublish the French node.
    $content_moderation_state = ContentModerationState::load(1);
    $content_moderation_state = $content_moderation_state->getTranslation('fr');
    $content_moderation_state->set('moderation_state', 'draft');
    $content_moderation_state->setNewRevision(TRUE);
    // Revision 8 (en, fr).
    $content_moderation_state->save();

    $english_node = $this->reloadNode($english_node, $english_node->getRevisionId());
    $this->assertEquals('draft', $english_node->moderation_state->value);
    $french_node = $this->reloadNode($english_node, '8')->getTranslation('fr');
    $this->assertEquals('draft', $french_node->moderation_state->value);
    // Switching the moderation state to an unpublished state should update the
    // entity.
    $this->assertFalse($french_node->isPublished());

    // Get the default english node.
    $english_node = $this->reloadNode($english_node);
    $this->assertTrue($english_node->isPublished());
    $this->assertEquals(6, $english_node->getRevisionId());
  }

  /**
   * Tests that a non-translatable entity type with a langcode can be moderated.
   */
  public function testNonTranslatableEntityTypeModeration() {
    // Make the 'entity_test_with_bundle' entity type revisionable.
    $entity_type = clone \Drupal::entityTypeManager()->getDefinition('entity_test_with_bundle');
    $keys = $entity_type->getKeys();
    $keys['revision'] = 'revision_id';
    $entity_type->set('entity_keys', $keys);
    \Drupal::state()->set('entity_test_with_bundle.entity_type', $entity_type);
    \Drupal::entityDefinitionUpdateManager()->applyUpdates();

    // Create a test bundle.
    $entity_test_bundle = EntityTestBundle::create([
      'id' => 'example',
    ]);
    $entity_test_bundle->save();

    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('entity_test_with_bundle', 'example');
    $workflow->save();

    // Check that the tested entity type is not translatable.
    $entity_type = \Drupal::entityTypeManager()->getDefinition('entity_test_with_bundle');
    $this->assertFalse($entity_type->isTranslatable(), 'The test entity type is not translatable.');

    // Create a test entity.
    $entity_test_with_bundle = EntityTestWithBundle::create([
      'type' => 'example'
    ]);
    $entity_test_with_bundle->save();
    $this->assertEquals('draft', $entity_test_with_bundle->moderation_state->value);

    $entity_test_with_bundle->moderation_state->value = 'published';
    $entity_test_with_bundle->save();

    $this->assertEquals('published', EntityTestWithBundle::load($entity_test_with_bundle->id())->moderation_state->value);
  }

  /**
   * Tests that a non-translatable entity type without a langcode can be
   * moderated.
   */
  public function testNonLangcodeEntityTypeModeration() {
    // Make the 'entity_test_with_bundle' entity type revisionable and unset
    // the langcode entity key.
    $entity_type = clone \Drupal::entityTypeManager()->getDefinition('entity_test_with_bundle');
    $keys = $entity_type->getKeys();
    $keys['revision'] = 'revision_id';
    unset($keys['langcode']);
    $entity_type->set('entity_keys', $keys);
    \Drupal::state()->set('entity_test_with_bundle.entity_type', $entity_type);
    \Drupal::entityDefinitionUpdateManager()->applyUpdates();

    // Create a test bundle.
    $entity_test_bundle = EntityTestBundle::create([
      'id' => 'example',
    ]);
    $entity_test_bundle->save();

    $workflow = Workflow::load('editorial');
    $workflow->getTypePlugin()->addEntityTypeAndBundle('entity_test_with_bundle', 'example');
    $workflow->save();

    // Check that the tested entity type is not translatable.
    $entity_type = \Drupal::entityTypeManager()->getDefinition('entity_test_with_bundle');
    $this->assertFalse($entity_type->isTranslatable(), 'The test entity type is not translatable.');

    // Create a test entity.
    $entity_test_with_bundle = EntityTestWithBundle::create([
      'type' => 'example'
    ]);
    $entity_test_with_bundle->save();
    $this->assertEquals('draft', $entity_test_with_bundle->moderation_state->value);

    $entity_test_with_bundle->moderation_state->value = 'published';
    $entity_test_with_bundle->save();

    $this->assertEquals('published', EntityTestWithBundle::load($entity_test_with_bundle->id())->moderation_state->value);
  }

  /**
   * Reloads the node after clearing the static cache.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to reload.
   * @param int|false $revision_id
   *   The specific revision ID to load. Defaults FALSE and just loads the
   *   default revision.
   *
   * @return \Drupal\node\NodeInterface
   *   The reloaded node.
   */
  protected function reloadNode(NodeInterface $node, $revision_id = FALSE) {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $storage->resetCache([$node->id()]);
    if ($revision_id) {
      return $storage->loadRevision($revision_id);
    }
    return $storage->load($node->id());
  }

}
