<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Data Model
 *
 * @property \Cake\ORM\Association\BelongsTo $LocTypes
 * @property \Cake\ORM\Association\BelongsTo $Locs
 * @property \Cake\ORM\Association\BelongsTo $Categories
 * @property \Cake\ORM\Association\BelongsTo $Sources
 *
 * @method \App\Model\Entity\Data get($primaryKey, $options = [])
 * @method \App\Model\Entity\Data newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Data[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Data|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Data patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Data[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Data findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DataTable extends Table
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

        $this->setTable('data');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('LocTypes', [
            'foreignKey' => 'loc_type_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Locs', [
            'foreignKey' => 'loc_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Sources', [
            'foreignKey' => 'source_id',
            'joinType' => 'INNER'
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
            ->integer('loc_id')
            ->requirePresence('loc_id', 'create')
            ->notEmpty('loc_id');

        $validator
            ->integer('loc_type_id')
            ->requirePresence('loc_type_id', 'create')
            ->notEmpty('loc_type_id');

        $validator
            ->integer('category_id')
            ->requirePresence('category_id', 'create')
            ->notEmpty('category_id');

        $validator
            ->integer('source_id')
            ->requirePresence('source_id', 'create')
            ->notEmpty('source_id');

        $validator
            ->integer('survey_date')
            ->requirePresence('survey_date', 'create')
            ->notEmpty('survey_date');

        $validator
            ->decimal('value')
            ->requirePresence('value', 'create')
            ->notEmpty('value');

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
        $rules->add($rules->existsIn(['loc_type_id'], 'LocationTypes'));
        $rules->add($rules->existsIn(['category_id'], 'DataCategories'));
        $rules->add($rules->existsIn(['source_id'], 'Sources'));

        return $rules;
    }
}
