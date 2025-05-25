# StrSim

A collection of string similarity and distance algorithms implemented in PHP. This library provides standalone static methods for computing various similarity metrics, useful in natural language processing, fuzzy matching, spell checking, and bioinformatics.

---

## Requirements

- PHP 8.3+
- Composer

## Installation

1. Use the library via Composer:

```bash
composer require edgaras/strsim
```

2. Include the Composer autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Supported Algorithms

| Class               | Method                     | Description                                                          |
|--------------------|----------------------------|----------------------------------------------------------------------|
| `Levenshtein`      | `distance()`               | Measures the number of insertions, deletions, or substitutions.      |
| `DamerauLevenshtein` | `distance()`             | Levenshtein with transpositions included.                            |
| `Hamming`          | `distance()`               | Counts differing positions (requires equal-length strings).          |
| `Jaro`             | `distance()`               | Measures similarity based on character matches and transpositions.   |
| `JaroWinkler`      | `distance()`               | Jaro with a prefix match boost for similar string starts.            |
| `LCS`              | `length()`                 | Returns the length of the longest common subsequence.                |
| `SmithWaterman`    | `score()`                  | Local alignment scoring for best-matching subsequences.              |
| `NeedlemanWunsch`  | `score()`                  | Global alignment scoring for entire string similarity.               |
| `Cosine`           | `similarity()`             | Measures similarity via character frequency vectors.                 |
| `Cosine`           | `similarityFromVectors()`  | Computes cosine similarity for numeric vector inputs.                |
| `Jaccard`          | `index()`                  | Ratio of shared to total unique characters.                          |
| `MongeElkan`       | `similarity()`             | Average best-word similarity using Jaro-Winkler internally.          |

## Usage

```php
use Edgaras\StrSim\Levenshtein;
use Edgaras\StrSim\DamerauLevenshtein;
use Edgaras\StrSim\Hamming;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;
use Edgaras\StrSim\LCS;
use Edgaras\StrSim\SmithWaterman;
use Edgaras\StrSim\NeedlemanWunsch;
use Edgaras\StrSim\Cosine;
use Edgaras\StrSim\Jaccard;
use Edgaras\StrSim\MongeElkan;

// Detecting spelling error distance in user input
Levenshtein::distance("kitten", "sitting");  

// Detecting typo distance with transposition correction
DamerauLevenshtein::distance("abcd", "acbd");  

// Bit-level error detection (equal-length only)
Hamming::distance("1011101", "1001001");  

// Comparing short strings with transposition support
Jaro::distance("dixon", "dicksonx");  

// Matching names with common prefixes
JaroWinkler::distance("martha", "marhta");  

// Finding common subsequence in DNA fragments
LCS::length("ACCGGTCGAGTGCGCGGAAGCCGGCCGAA", "GTCGTTCGGAATGCCGTTGCTCTGTAAA"); 

// Local alignment score for substring match
SmithWaterman::score("ACACACTA", "AGCACACA");  

// Global alignment score for complete sequence match
NeedlemanWunsch::score("GATTACA", "GCATGCU");  

// Comparing word frequency in short texts
Cosine::similarity("night", "nacht");  

// Comparing embedding vectors from NLP model
Cosine::similarityFromVectors([0.1, 0.2, 0.3], [0.1, 0.3, 0.4]);  

// Comparing token overlap in short strings
Jaccard::index("abc", "bcd"); 

// Fuzzy match between two multi-word names
MongeElkan::similarity("john smith", "jon smythe");  

```

## Useful links

- [Levenshtein](https://en.wikipedia.org/wiki/Levenshtein_distance) 
- [Damerau–Levenshtein](https://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance) 
- [Hamming](https://en.wikipedia.org/wiki/Hamming_distance)  
- [Jaro](https://en.wikipedia.org/wiki/Jaro%E2%80%93Winkler_distance)  
- [Jaro–Winkler](https://en.wikipedia.org/wiki/Jaro%E2%80%93Winkler_distance)  
- [Longest Common Subsequence (LCS)](https://en.wikipedia.org/wiki/Longest_common_subsequence)  
- [Smith–Waterman](https://en.wikipedia.org/wiki/Smith%E2%80%93Waterman_algorithm)  
- [Needleman–Wunsch](https://en.wikipedia.org/wiki/Needleman%E2%80%93Wunsch_algorithm)  
- [Cosine Similarity](https://en.wikipedia.org/wiki/Cosine_similarity)  
- [Jaccard Index](https://en.wikipedia.org/wiki/Jaccard_index)  
- [Monge–Elkan](https://en.wikipedia.org/wiki/Monge%E2%80%93Elkan_algorithm)  
