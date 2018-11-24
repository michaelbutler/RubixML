<?php

namespace Rubix\ML\Classifiers;

use Rubix\ML\Learner;
use Rubix\ML\Ensemble;
use Rubix\ML\Persistable;
use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Other\Helpers\Stats;
use Rubix\ML\Other\Functions\Argmax;
use InvalidArgumentException;
use RuntimeException;

/**
 * Random Forest
 *
 * Ensemble classifier that trains Decision Trees (Classification Trees or Extra
 * Trees) on a random subset of the training data. A prediction is made based on
 * the average probability score returned from each tree in the forest.
 *
 * References:
 * [1] L. Breiman. (2001). Random Forests.
 * [2] L. Breiman et al. (2005). Extremely Randomized Trees.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class RandomForest implements Learner, Ensemble, Probabilistic, Persistable
{
    const AVAILABLE_ESTIMATORS = [
        ClassificationTree::class,
        ExtraTreeClassifier::class,
    ];

    /**
     * The base estimator.
     *
     * @var \Rubix\ML\Learner
     */
    protected $base;

    /**
     * The number of trees to train in the ensemble.
     *
     * @var int
     */
    protected $estimators;

    /**
     * The ratio of training samples to train each decision tree on.
     *
     * @var float
     */
    protected $ratio;

    /**
     * The possible class outcomes.
     *
     * @var array
     */
    protected $classes = [
        //
    ];

    /**
     * The decision trees that make up the forest.
     *
     * @var array
     */
    protected $forest = [
        //
    ];

    /**
     * @param  \Rubix\ML\Learner|null  $base
     * @param  int  $estimators
     * @param  float  $ratio
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(?Learner $base = null, int $estimators = 100, float $ratio = 0.1)
    {
        if (is_null($base)) {
            $base = new ClassificationTree();
        }

        if (!in_array(get_class($base), self::AVAILABLE_ESTIMATORS)) {
            throw new InvalidArgumentException('Base estimator is not'
                . ' compatible with this ensemble.');
        }

        if ($estimators < 1) {
            throw new InvalidArgumentException('The number of estimators in the'
                . " ensemble cannot be less than 1, $estimators given.");
        }

        if ($ratio < 0.01 or $ratio > 1.) {
            throw new InvalidArgumentException('Subsample ratio must be between'
                . " 0.01 and 1, $ratio given.");
        }

        $this->base = $base;
        $this->estimators = $estimators;
        $this->ratio = $ratio;
    }

    /**
     * Return the integer encoded type of estimator this is.
     *
     * @return int
     */
    public function type() : int
    {
        return self::CLASSIFIER;
    }

    /**
     * Return the feature importances calculated during training keyed by
     * feature column.
     * 
     * @throws \RuntimeException
     * @return array
     */
    public function featureImportances() : array
    {
        if (empty($this->forest)) {
            throw new RuntimeException('Estimator has not been trained.');
        }

        $k = count($this->forest);

        $importances = [];

        foreach ($this->forest as $tree) {
            foreach ($tree->featureImportances() as $column => $value) {
                if (isset($importances[$column])) {
                    $importances[$column] += $value;
                } else {
                    $importances[$column] = $value;
                }
            }
        }

        foreach ($importances as &$importance) {
            $importance /= $k;
        }

        return $importances;
    }

    /**
     * Train a Random Forest by training an ensemble of decision trees on random
     * subsets of the training data.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $dataset
     * @throws \InvalidArgumentException
     * @return void
     */
    public function train(Dataset $dataset) : void
    {
        if (!$dataset instanceof Labeled) {
            throw new InvalidArgumentException('This estimator requires a'
                . ' labeled training set.');
        }

        $this->classes = $dataset->possibleOutcomes();

        $k = (int) round($this->ratio * $dataset->numRows());

        $this->forest = [];

        for ($epoch = 0; $epoch < $this->estimators; $epoch++) {
            $tree = clone $this->base;

            $subset = $dataset->randomSubsetWithReplacement($k);

            $tree->train($subset);

            $this->forest[] = $tree;
        }
    }

    /**
     * Make predictions from a dataset.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $dataset
     * @return array
     */
    public function predict(Dataset $dataset) : array
    {
        return array_map([Argmax::class, 'compute'], $this->proba($dataset));
    }

    /**
     * Estimate probabilities for each possible outcome.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $dataset
     * @throws \RuntimeException
     * @return array
     */
    public function proba(Dataset $dataset) : array
    {
        if (empty($this->forest)) {
            throw new RuntimeException('Estimator has not been trained.');
        }

        $probabilities = array_fill(0, $dataset->numRows(),
            array_fill_keys($this->classes, 0.));

        foreach ($this->forest as $tree) {
            foreach ($tree->proba($dataset) as $i => $joint) {
                foreach ($joint as $class => $probability) {
                    $probabilities[$i][$class] += $probability;
                }
            }
        }

        $k = count($this->forest);

        foreach ($probabilities as &$joint) {
            foreach ($joint as &$probability) {
                $probability /= $k;
            }
        }

        return $probabilities;
    }
}
