<span style="float:right;"><a href="https://github.com/RubixML/RubixML/blob/master/src/Transformers/KNNImputer.php">Source</a></span>

# KNN Imputer
An unsupervised imputer that replaces NaN values in numerical datasets with the weighted average of the sample's k nearest neighbors.

**Note:** This transformer is only compatible with NaN safe distance kernels such as [Safe Euclidean](../kernels/distance/safe-euclidean.md).

**Interfaces:** [Transformer](api.md#transformers), [Stateful](api.md#stateful), [Elastic](api.md#elastic)

**Data Type Compatibility:** Depends on distance kernel

### Parameters
| # | Param | Default | Type | Description |
|---|---|---|---|---|
| 1 | k | 5 | int | The number of nearest neighbors to consider when imputing a value. |
| 2 | weighted | true | bool | Should we use the inverse distances as confidence scores imputing values? |
| 3 | kernel | Safe Euclidean | object | The NaN safe distance kernel used to compute the distance between sample points. |
| 4 | placeholder | '?' | string | The categorical placeholder variable denoting the category that contains missing values. |

### Additional Methods
This transformer does not have any additional methods.

### Example
```php
use Rubix\ML\Transformers\KNNImputer;
use Rubix\ML\Kernels\Distance\Gower;

$transformer = new KNNImputer(10, false, new Gower(), '?');
```

### References
>- O. Troyanskaya et al. (2001). Missing value estimation methods for DNA microarrays.