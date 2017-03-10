<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DataCategories Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentDataCategories
 * @property \Cake\ORM\Association\HasMany $ChildDataCategories
 *
 * @method \App\Model\Entity\DataCategory get($primaryKey, $options = [])
 * @method \App\Model\Entity\DataCategory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DataCategory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DataCategory|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DataCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DataCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DataCategory findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class DataCategoriesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('data_categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');

        $this->belongsTo('ParentDataCategories', [
            'className' => 'DataCategories',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildDataCategories', [
            'className' => 'DataCategories',
            'foreignKey' => 'parent_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('store_type', 'create')
            ->notEmpty('store_type');

        $validator
            ->requirePresence('display_type', 'create')
            ->notEmpty('display_type');

        $validator
            ->integer('display_precision')
            ->requirePresence('display_precision', 'create')
            ->notEmpty('display_precision');

        $validator
            ->boolean('is_group')
            ->requirePresence('is_group', 'create')
            ->notEmpty('is_group');

        $validator
            ->requirePresence('notes', 'create')
            ->notEmpty('notes');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentDataCategories'));

        return $rules;
    }
}
