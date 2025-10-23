# Mobile Exam App - API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
This API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header for protected routes:
```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### 1. Login
**POST** `/api/login`

Authenticate a student and receive an access token.

**Request Body:**
```json
{
  "login": "student@email.com",  // Can be email or student ID number
  "password": "password123",
  "device_name": "mobile-app"     // Optional
}
```

**Response (200):**
```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "student_id": 1,
    "id_number": "2021-00001",
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "middle_name": "Santos",
    "email": "student@email.com",
    "status": "Enrolled"
  }
}
```

**Error Response (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "login": ["The provided credentials are incorrect."]
  }
}
```

---

### 2. Logout
**POST** `/api/logout`

Revoke the current access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

### 3. Get Current User
**GET** `/api/me`

Get the authenticated user's information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "student_id": 1,
    "id_number": "2021-00001",
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "middle_name": "Santos",
    "email": "student@email.com",
    "status": "Enrolled"
  }
}
```

---

## Class Endpoints

### 4. Get Enrolled Classes
**GET** `/api/classes`

Get all classes the student is enrolled in.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "classes": [
    {
      "class_id": 1,
      "title": "BSCS 4A - Data Structures",
      "year_level": 4,
      "section": "A",
      "semester": 1,
      "school_year": "2024-2025",
      "subject": {
        "id": 10,
        "code": "CS101",
        "name": "Data Structures and Algorithms"
      }
    }
  ]
}
```

---

## Exam Endpoints

### 5. Get Available Exams
**GET** `/api/exams`

Get all exams available to the authenticated student.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "exams": [
    {
      "assignment_id": 1,
      "exam_id": 5,
      "title": "Midterm Exam",
      "description": "Covers chapters 1-5",
      "subject": {
        "id": 10,
        "code": "CS101",
        "name": "Data Structures and Algorithms"
      },
      "class": {
        "id": 1,
        "title": "BSCS 4A - Data Structures"
      },
      "schedule_start": "2024-03-15 09:00:00",
      "schedule_end": "2024-03-15 11:00:00",
      "duration": 120,
      "total_points": 100,
      "no_of_items": 50,
      "status": "available",  // available, scheduled, in_progress, completed, expired
      "attempt": null
    },
    {
      "assignment_id": 2,
      "exam_id": 6,
      "title": "Final Exam",
      "description": "Comprehensive exam",
      "subject": {
        "id": 10,
        "code": "CS101",
        "name": "Data Structures"
      },
      "class": {
        "id": 1,
        "title": "BSCS 4A"
      },
      "schedule_start": "2024-05-20 09:00:00",
      "schedule_end": "2024-05-20 11:00:00",
      "duration": 120,
      "total_points": 150,
      "no_of_items": 75,
      "status": "completed",
      "attempt": {
        "attempt_id": 15,
        "start_time": "2024-05-20 09:05:00",
        "end_time": "2024-05-20 10:45:00",
        "score": 135,
        "status": "submitted"
      }
    }
  ]
}
```

**Exam Status Values:**
- `scheduled` - Exam not yet started (before schedule_start)
- `available` - Exam is currently available to take
- `in_progress` - Student has started but not submitted
- `completed` - Student has submitted the exam
- `expired` - Exam period has ended (after schedule_end)

---

### 6. Get Exam Details
**GET** `/api/exams/{examId}`

Get detailed information about a specific exam, including all questions.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "exam": {
    "exam_id": 5,
    "title": "Midterm Exam",
    "description": "Covers chapters 1-5",
    "subject": {
      "id": 10,
      "code": "CS101",
      "name": "Data Structures"
    },
    "schedule_start": "2024-03-15 09:00:00",
    "schedule_end": "2024-03-15 11:00:00",
    "duration": 120,
    "total_points": 100,
    "no_of_items": 50,
    "sections": [
      {
        "section_id": 1,
        "title": "Multiple Choice",
        "directions": "Choose the best answer for each question.",
        "order": 1,
        "items": [
          {
            "item_id": 1,
            "question": "What is a stack?",
            "item_type": "mcq",
            "options": [
              "A) LIFO data structure",
              "B) FIFO data structure",
              "C) Random access structure",
              "D) None of the above"
            ],
            "points_awarded": 2,
            "order": 1
          },
          {
            "item_id": 2,
            "question": "Binary search requires sorted data.",
            "item_type": "torf",
            "options": ["True", "False"],
            "points_awarded": 1,
            "order": 2
          }
        ]
      },
      {
        "section_id": 2,
        "title": "Essay",
        "directions": "Answer in 3-5 sentences.",
        "order": 2,
        "items": [
          {
            "item_id": 15,
            "question": "Explain the difference between an array and a linked list.",
            "item_type": "essay",
            "options": null,
            "points_awarded": 10,
            "order": 1
          }
        ]
      }
    ]
  },
  "attempt": null
}
```

**Item Types:**
- `mcq` - Multiple Choice Question
- `torf` - True or False
- `enum` - Enumeration (list of items)
- `iden` - Identification (short answer)
- `essay` - Essay (long answer)

**Error Response (404):**
```json
{
  "message": "Exam not found or not accessible"
}
```

---

## Exam Attempt Endpoints

### 7. Start Exam Attempt
**POST** `/api/exam-attempts`

Start a new exam attempt or resume an existing in-progress attempt.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "exam_assignment_id": 1
}
```

**Response (201):**
```json
{
  "attempt": {
    "attempt_id": 25,
    "exam_assignment_id": 1,
    "student_id": 1,
    "start_time": "2024-03-15 09:05:00",
    "status": "in_progress"
  },
  "message": "Exam attempt started successfully"
}
```

**Error Responses:**

Already completed (400):
```json
{
  "message": "You have already completed this exam"
}
```

Not started yet (400):
```json
{
  "message": "Exam has not started yet"
}
```

Expired (400):
```json
{
  "message": "Exam has ended"
}
```

No access (403):
```json
{
  "message": "You do not have access to this exam"
}
```

---

### 8. Submit Exam
**POST** `/api/exam-attempts/{attemptId}/submit`

Submit answers for an exam attempt.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "answers": [
    {
      "item_id": 1,
      "answer": "A"
    },
    {
      "item_id": 2,
      "answer": "True"
    },
    {
      "item_id": 3,
      "answer": ["Stack", "Queue", "Tree"]
    },
    {
      "item_id": 15,
      "answer": "Arrays have fixed size and contiguous memory..."
    }
  ]
}
```

**Response (200):**
```json
{
  "message": "Exam submitted successfully",
  "attempt": {
    "attempt_id": 25,
    "start_time": "2024-03-15 09:05:00",
    "end_time": "2024-03-15 10:50:00",
    "score": 85,
    "status": "submitted"
  }
}
```

**Notes on Auto-Grading:**
- MCQ and True/False: Auto-graded (exact match)
- Enumeration and Identification: Auto-graded (case-insensitive)
- Essay: Not auto-graded (requires manual grading by teacher)

**Error Responses:**

Attempt not found (404):
```json
{
  "message": "Exam attempt not found"
}
```

Unauthorized (403):
```json
{
  "message": "Unauthorized"
}
```

Already submitted (400):
```json
{
  "message": "Exam already submitted"
}
```

---

### 9. Get Exam Results
**GET** `/api/exam-attempts/{attemptId}/results`

Get the results of a submitted exam attempt.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "attempt": {
    "attempt_id": 25,
    "start_time": "2024-03-15 09:05:00",
    "end_time": "2024-03-15 10:50:00",
    "score": 85,
    "total_points": 100,
    "percentage": 85.0,
    "status": "submitted"
  },
  "exam": {
    "exam_id": 5,
    "title": "Midterm Exam",
    "subject": {
      "code": "CS101",
      "name": "Data Structures"
    }
  }
}
```

**Error Responses:**

Not submitted yet (400):
```json
{
  "message": "Exam not yet submitted"
}
```

Not found (404):
```json
{
  "message": "Exam attempt not found"
}
```

---

## Common Error Responses

### 401 Unauthorized
Missing or invalid token:
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
Account not active:
```json
{
  "message": "Your account is not active. Please contact administration."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message"
    ]
  }
}
```

---

## Mobile App Integration Guide

### 1. Authentication Flow
```
1. User enters email/ID and password
2. POST /api/login
3. Store token securely (keychain/secure storage)
4. Include token in all subsequent requests
```

### 2. Displaying Available Exams
```
1. GET /api/exams
2. Filter by status (show "available" and "scheduled" prominently)
3. Display exam cards with title, subject, schedule, duration
4. Show different UI for completed exams vs available
```

### 3. Taking an Exam
```
1. User taps "Start Exam" button
2. POST /api/exam-attempts with exam_assignment_id
3. GET /api/exams/{examId} to fetch questions
4. Display questions section by section
5. Store answers locally as user progresses
6. When ready to submit: POST /api/exam-attempts/{attemptId}/submit
7. Navigate to results screen
8. GET /api/exam-attempts/{attemptId}/results to show score
```

### 4. Timer Implementation
```
- Calculate time remaining: (start_time + duration_minutes) - current_time
- Show countdown timer
- Auto-submit when time expires
- Warn user at 5 minutes and 1 minute remaining
```

### 5. Offline Considerations
```
- Store exam questions locally after fetching
- Allow answering questions offline
- Queue submission for when online
- Sync answers when connection restored
```

### 6. Error Handling
```
- Handle 401: Clear token, redirect to login
- Handle 403: Show "access denied" message
- Handle 400: Show specific error message to user
- Handle network errors: Show retry option
```

---

## Sample Mobile App Workflow

### Student Login
```dart
// Example in Flutter/Dart
Future<void> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/login'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'login': email,
      'password': password,
      'device_name': 'mobile-app'
    }),
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    await storage.write(key: 'token', value: data['token']);
    await storage.write(key: 'user', value: jsonEncode(data['user']));
  }
}
```

### Fetching Exams
```dart
Future<List<Exam>> fetchExams() async {
  final token = await storage.read(key: 'token');
  final response = await http.get(
    Uri.parse('$baseUrl/api/exams'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json'
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return (data['exams'] as List)
        .map((json) => Exam.fromJson(json))
        .toList();
  }
  throw Exception('Failed to load exams');
}
```

### Starting an Exam
```dart
Future<ExamAttempt> startExam(int assignmentId) async {
  final token = await storage.read(key: 'token');
  final response = await http.post(
    Uri.parse('$baseUrl/api/exam-attempts'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json'
    },
    body: jsonEncode({'exam_assignment_id': assignmentId}),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    return ExamAttempt.fromJson(data['attempt']);
  }
  throw Exception('Failed to start exam');
}
```

### Submitting Answers
```dart
Future<void> submitExam(int attemptId, List<Answer> answers) async {
  final token = await storage.read(key: 'token');
  final response = await http.post(
    Uri.parse('$baseUrl/api/exam-attempts/$attemptId/submit'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json'
    },
    body: jsonEncode({
      'answers': answers.map((a) => a.toJson()).toList()
    }),
  );

  if (response.statusCode != 200) {
    throw Exception('Failed to submit exam');
  }
}
```

---

## Security Best Practices

1. **Store tokens securely** - Use platform-specific secure storage
2. **Use HTTPS** - Always use HTTPS in production
3. **Token expiration** - Handle token expiration gracefully
4. **Validate on backend** - Never trust client-side validation alone
5. **Rate limiting** - Be mindful of API rate limits

---

## Testing the API

You can test the API using tools like Postman or curl:

```bash
# Login
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"login":"student@email.com","password":"password123"}'

# Get exams (replace TOKEN with actual token)
curl -X GET http://your-domain.com/api/exams \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"
```

---

## Support

For issues or questions about the API, please contact the development team.
