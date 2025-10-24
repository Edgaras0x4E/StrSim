# StrSim v1.1.1

A collection of string similarity and distance algorithms implemented in PHP with full Unicode and multibyte character support. This library provides standalone static methods for computing various similarity metrics, useful in natural language processing, fuzzy matching, spell checking, and bioinformatics.

## What's New in v1.1.1

### 🔧 **Fixed Naming Issues** 
- **Fixed `Jaro::distance()`** - Previously returned similarity values (1.0 = identical), now correctly returns distance values (0.0 = identical)
- **Fixed `JaroWinkler::distance()`** - Previously returned similarity values (1.0 = identical), now correctly returns distance values (0.0 = identical)

### ✨ **New Functions Added**
- **`Jaro::similarity()`** - Returns proper similarity values (1.0 = identical, 0.0 = completely different)
- **`JaroWinkler::similarity()`** - Returns proper similarity values (1.0 = identical, 0.0 = completely different)

### 📚 **Improvements**
- **Better MongeElkan** - Fixed edge cases for empty string comparisons

### 🔄 **Migration Guide**
If you were using `Jaro::distance()` or `JaroWinkler::distance()` expecting similarity values (where 1.0 = identical):
- **Before**: `Jaro::distance("hello", "hello")` returned `1.0`
- **After**: Use `Jaro::similarity("hello", "hello")` to get `1.0`, or `Jaro::distance("hello", "hello")` returns `0.0`

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

## Features

- **Full Unicode Support**: All algorithms handle multibyte characters, emoji, combining marks, and complex grapheme clusters
- **UTF-8 Validation**: Automatic validation of input strings with clear error messages
- **Error Handling**: Proper exception types with descriptive messages
- **Code-Point Based**: Consistent behavior across all Unicode normalization forms
- **Optimized Tokenization**: Smart whitespace handling for text-based algorithms
- **Distance vs Similarity**: Clear distinction between distance measures (0 = identical) and similarity measures (1 = identical)

## Supported Algorithms

| Class               | Method                     | Return Range | Description                                                          |
|--------------------|----------------------------|--------------|----------------------------------------------------------------------|
| `Levenshtein`      | `distance()`               | 0 to ∞       | Number of insertions, deletions, or substitutions needed.           |
| `DamerauLevenshtein` | `distance()`             | 0 to ∞       | Levenshtein with transpositions included.                           |
| `Hamming`          | `distance()`               | 0 to ∞       | Number of differing positions (requires equal-length strings).      |
| `Jaro`             | `similarity()`             | 0.0 to 1.0   | Similarity based on character matches and transpositions.           |
| `Jaro`             | `distance()`               | 0.0 to 1.0   | Distance measure (1 - similarity).                                  |
| `JaroWinkler`      | `similarity()`             | 0.0 to 1.0   | Jaro with a prefix match boost for similar string starts.           |
| `JaroWinkler`      | `distance()`               | 0.0 to 1.0   | Distance measure (1 - similarity).                                  |
| `LCS`              | `length()`                 | 0 to ∞       | Length of the longest common subsequence.                           |
| `SmithWaterman`    | `score()`                  | 0 to ∞       | Local alignment scoring for best-matching subsequences.             |
| `NeedlemanWunsch`  | `score()`                  | -∞ to ∞      | Global alignment scoring for entire string similarity.              |
| `Cosine`           | `similarity()`             | 0.0 to 1.0   | Similarity via character frequency vectors.                         |
| `Cosine`           | `similarityFromVectors()`  | -1.0 to 1.0  | Cosine similarity for numeric vector inputs.                        |
| `Jaccard`          | `index()`                  | 0.0 to 1.0   | Ratio of shared to total unique characters.                         |
| `MongeElkan`       | `similarity()`             | 0.0 to 1.0   | Average best-word similarity using Jaro-Winkler internally.         |

## Understanding Distance vs Similarity

This library provides both **distance** and **similarity** measures for certain algorithms:

- **Distance measures**: Return `0.0` for identical strings and higher values for more different strings
  - Examples: `Levenshtein::distance()`, `Hamming::distance()`, `Jaro::distance()`, `JaroWinkler::distance()`
  
- **Similarity measures**: Return `1.0` for identical strings and lower values for more different strings  
  - Examples: `Cosine::similarity()`, `Jaccard::index()`, `Jaro::similarity()`, `JaroWinkler::similarity()`

For Jaro and Jaro-Winkler algorithms, both functions are available:
- `similarity()` returns values from 0.0 (completely different) to 1.0 (identical)
- `distance()` returns values from 0.0 (identical) to 1.0 (completely different)
- The relationship is: `distance = 1.0 - similarity`

## Usage

### Basic Usage

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
Levenshtein::distance("kitten", "sitting");  // Returns: 3

// Detecting typo distance with transposition correction
DamerauLevenshtein::distance("abcd", "acbd");  // Returns: 1

// Bit-level error detection (equal-length only)
Hamming::distance("1011101", "1001001");  // Returns: 2

// Comparing short strings with transposition support
Jaro::similarity("dixon", "dicksonx");  // Returns: 0.767 (similarity)
Jaro::distance("dixon", "dicksonx");    // Returns: 0.233 (distance = 1 - similarity)

// Matching names with common prefixes
JaroWinkler::similarity("martha", "marhta");  // Returns: 0.961 (similarity)
JaroWinkler::distance("martha", "marhta");    // Returns: 0.039 (distance = 1 - similarity)

// Finding common subsequence in DNA fragments
LCS::length("ACCGGTCGAGTGCGCGGAAGCCGGCCGAA", "GTCGTTCGGAATGCCGTTGCTCTGTAAA"); // Returns: 13

// Local alignment score for substring match
SmithWaterman::score("ACACACTA", "AGCACACA");  // Returns: 11

// Global alignment score for complete sequence match
NeedlemanWunsch::score("GATTACA", "GCATGCU");  // Returns: 0

// Comparing word frequency in short texts
Cosine::similarity("night", "nacht");  // Returns: 0.6

// Comparing embedding vectors from NLP model
Cosine::similarityFromVectors([0.1, 0.2, 0.3], [0.1, 0.3, 0.4]);  // Returns: 0.925

// Comparing token overlap in short strings
Jaccard::index("abc", "bcd"); // Returns: 0.5

// Fuzzy match between two multi-word names
MongeElkan::similarity("john smith", "jon smythe");  // Returns: 0.822
```

### Unicode and Multibyte Examples

```php
// All algorithms support Unicode characters
Levenshtein::distance("café", "caffe");  // Returns: 2
Levenshtein::distance("こんにちは", "こんにちわ");  // Returns: 1

// Emoji and complex characters
Levenshtein::distance("🚀🌟", "🚀⭐");  // Returns: 1
Hamming::distance("👍🏽", "👍🏾");  // Returns: 1

// Different scripts and languages
Jaro::similarity("привет", "привет");  // Returns: 1.0 (identical)
Jaro::distance("привет", "привет");    // Returns: 0.0 (no distance)
JaroWinkler::similarity("عربي", "عربى");  // Returns: 0.9 (high similarity)
JaroWinkler::distance("عربي", "عربى");    // Returns: 0.1 (low distance)

// ZWJ sequences and combining marks
Levenshtein::distance("👨‍👩‍👧‍👦", "👨👩👧👦");  // Returns: 3
Levenshtein::distance("é", "e\u{0301}");  // Returns: 2
```

### Custom Scoring

```php
// Smith-Waterman with custom scoring
SmithWaterman::score("ACGT", "ACGT", match: 5, mismatch: -2, gap: -1);  // Returns: 20

// Needleman-Wunsch with custom parameters
NeedlemanWunsch::score("ACGT", "ACGT", match: 3, mismatch: -1, gap: -2);  // Returns: 12

// Jaro-Winkler with custom prefix scaling
JaroWinkler::similarity("prefix_test", "prefix_demo", 0.2);  // Custom scale factor for similarity
JaroWinkler::distance("prefix_test", "prefix_demo", 0.2);    // Custom scale factor for distance
```

### Error Handling

```php
try {
    // This will throw InvalidArgumentException for unequal lengths
    Hamming::distance("abc", "abcd");
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "Strings must be of equal length."
}

try {
    // This will throw InvalidArgumentException for invalid UTF-8
    Levenshtein::distance("valid", "\xFF\xFF");
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "Input strings must be valid UTF-8."
}

try {
    // This will throw InvalidArgumentException for mismatched vector lengths
    Cosine::similarityFromVectors([1, 2], [1, 2, 3]);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "Vectors must be the same length."
}
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
