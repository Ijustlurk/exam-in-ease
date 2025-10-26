# Answer Validation Fix - Mobile App Integration

## Problem
The API was incorrectly validating student answers from the mobile app, causing correct answers to be marked as wrong.

## Root Cause Analysis

### Data Flow Discovery
1. **Frontend (Instructor creates questions):**
   - MCQ: Checkboxes with values `0, 1, 2, 3` to mark correct options
   - TORF: Radio buttons for "true" or "false"
   - JavaScript stores in database:
     - `options`: `{"0":"Option A text", "1":"Option B text", ...}`
     - `answer`: `[0, 1, 2]` (array of correct indices for MCQ) or `{"correct":"true"}` for TORF

2. **Mobile App (Student submits answers):**
   - MCQ: Sends option **keys** as letters: `"A"`, `"B"`, `"C"`, `"D"`
   - TORF: Sends: `"True"` or `"False"`
   - Enumeration: Sends comma-separated string: `"Red, Blue, Green"`
   - Identification: Sends plain text: `"Paris"`

3. **Database Storage:**
   - MCQ: `answer` field = `"[0,1,2]"` (JSON array of indices)
   - TORF: `answer` field = `"{\"correct\":\"true\"}"` (JSON object)
   - Enum/Iden: `expected_answer` field = text or comma-separated string

### The Mismatch
The API was comparing:
- Mobile sends: `"A"` (letter key)
- Database has: `[0, 1]` (indices)
- Previous code tried to compare directly → **Always failed!**

## Solution Implemented

### Updated `submitAttempt()` Method in `Api/ExamController.php`

#### 1. MCQ (Multiple Choice Questions)
```php
case 'mcq':
    // Mobile app sends: "A", "B", "C", "D"
    // Database stores: [0, 1, 2, 3] (array of correct indices)
    
    // Convert letter to index (A=0, B=1, C=2, D=3)
    if (preg_match('/^[A-Z]$/i', $studentAnswer)) {
        $studentIndex = ord(strtoupper($studentAnswer)) - ord('A');
    }
    
    // Compare against database array of correct indices
    $correctIndices = $item->answer; // e.g., [0, 2]
    $isCorrect = in_array($studentIndex, $correctIndices);
```

**Key Changes:**
- ✅ Converts letter keys (A, B, C, D) to indices (0, 1, 2, 3)
- ✅ Uses `answer` field instead of `expected_answer`
- ✅ Handles multiple correct answers (array comparison)

#### 2. TORF (True or False)
```php
case 'torf':
    // Mobile app sends: "True" or "False"
    // Database stores: {"correct":"true"} or {"correct":"false"}
    
    $correctAnswerData = $item->answer; // JSON object
    $correctValue = $correctAnswerData['correct'];
    
    // Case-insensitive comparison
    $isCorrect = strtolower($studentAnswer) === strtolower($correctValue);
```

**Key Changes:**
- ✅ Uses `answer` field (JSON object with "correct" key)
- ✅ Case-insensitive comparison ("True" matches "true")

#### 3. Enumeration
```php
case 'enum':
    // Mobile app sends: "Red, Blue, Green" (comma-separated)
    // Database stores: comma-separated text in expected_answer
    
    // Convert string to array
    $studentAnswer = array_map('trim', explode(',', $studentAnswer));
    $correctAnswer = array_map('trim', explode(',', $correctAnswer));
    
    // Case-insensitive array intersection
    $isCorrect = count(array_intersect(
        array_map('strtolower', $studentAnswer),
        array_map('strtolower', $correctAnswer)
    )) === count($correctAnswer);
```

**Key Changes:**
- ✅ Parses comma-separated strings
- ✅ Case-insensitive comparison
- ✅ Order-independent matching

#### 4. Identification
```php
case 'iden':
    // Mobile app sends: "Paris"
    // Database stores: "Paris" in expected_answer
    
    $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($correctAnswer));
```

**Key Changes:**
- ✅ Case-insensitive comparison
- ✅ Trims whitespace

#### 5. Essay
```php
case 'essay':
    // Essays need manual grading
    $isCorrect = null; // null indicates needs manual grading
```

**No changes** - essays remain manually graded.

## Database Schema Reference

### `exam_items` Table
```sql
item_id             INT PRIMARY KEY
exam_id             INT
item_type           ENUM('mcq','torf','enum','iden','essay')
question            TEXT
options             TEXT            -- JSON: {"0":"text","1":"text",...}
answer              TEXT            -- MCQ: [0,1,2]  TORF: {"correct":"true"}
expected_answer     TEXT            -- For enum, iden, essay
points_awarded      INT
```

### Field Usage by Question Type
| Type | `options` | `answer` | `expected_answer` |
|------|-----------|----------|-------------------|
| MCQ  | JSON object {"0":"...", "1":"..."} | JSON array [0,1,2] | Not used |
| TORF | {"0":"True","1":"False"} | {"correct":"true"} | Not used |
| Enum | Not used | Not used | Comma-separated text |
| Iden | Not used | Not used | Text answer |
| Essay | Not used | Not used | Sample answer (optional) |

## Testing Checklist

- [ ] MCQ with single correct answer (mobile sends "A")
- [ ] MCQ with multiple correct answers (mobile sends "B", database has [1,3])
- [ ] TORF with "True" answer
- [ ] TORF with "False" answer
- [ ] Enumeration with exact match
- [ ] Enumeration with different order
- [ ] Enumeration with different case
- [ ] Identification with exact match
- [ ] Identification with different case
- [ ] Essay (should not auto-grade)

## Related Files Modified
1. `app/Http/Controllers/Api/ExamController.php` - `submitAttempt()` method (lines 596-685)
2. `database/migrations/2025_10_25_222846_add_enum_type_to_exam_items_table.php` - Added enum_type column
3. `app/Models/ExamItem.php` - Added enum_type to fillable array

## Additional Changes Made
Also fixed exam status filtering in 6 methods to include both 'approved' and 'ongoing' exams:
- `index()`, `show()`, `verifyOtp()`, `completedExams()`, `startAttempt()`, `submitAttempt()`

Added `enum_type` column to distinguish between ordered and unordered enumeration questions:
- **Ordered**: Student answers must match in exact order
- **Unordered**: Student answers can be in any order

## References
- Mobile app format: `STUDENT_ANSWER_FORMAT.md`
- Database structure: `database/migrations/2025_10_07_072735_create_exam_items_table.php`
- Frontend form: `resources/views/instructor/exam/question-modal.blade.php`
