# Exam-In-Ease User Manual

## Table of Contents
- [Introduction](#introduction)
- [User Roles Overview](#user-roles-overview)
- [Instructor Guide](#instructor-guide)
- [Program Chair Guide](#program-chair-guide)
- [Admin Guide](#admin-guide)
- [Frequently Asked Questions](#frequently-asked-questions)

---

## Introduction

**Exam-In-Ease** is a comprehensive exam management system designed to streamline the process of creating, approving, administering, and analyzing academic examinations. This manual provides detailed instructions for Instructors, Program Chairs, and Administrators.

### Key Features
- **Exam Creation**: Build exams with multiple question types
- **Approval Workflow**: Structured review and approval process
- **Real-time Analytics**: Comprehensive exam and question statistics
- **Collaboration**: Multiple instructors can work on exams together
- **Mobile Access**: Students can take exams on mobile devices
- **Automated Grading**: Instant results for objective questions

---

## User Roles Overview

### Instructor (Teacher)
- Creates and manages exam content
- Collaborates with other instructors
- Submits exams for approval
- Views and analyzes exam results

### Program Chair (Curriculum Manager)
- Reviews exams submitted for approval
- Approves or requests revisions
- Sets exam schedules and passwords
- Manages approval workflow

### Admin
- Manages users, classes, and subjects
- Oversees system-wide operations
- Handles user imports and enrollments
- Does not directly manage exam content

---

## Instructor Guide

### Getting Started

#### Accessing Your Dashboard
1. Log in to the system with your instructor credentials
2. Navigate to the **Instructor Dashboard**
3. You'll see your list of exams and key statistics

---

### Creating an Exam

#### Step 1: Create a New Exam

1. Click **"Create New Exam"** or **"New Exam"** button
2. Fill in the required information:
   - **Exam Title** (required, max 200 characters)
     - Example: "Midterm Examination - Mathematics"
   - **Exam Description** (optional)
     - Example: "This exam covers Chapters 1-5"
   - **Subject** (required)
     - Select from dropdown list
   - **Assigned Classes** (required, at least 1)
     - Select one or more classes
   - **Duration** (required, in minutes)
     - Example: 90 (for 1.5 hours)
   - **Schedule Start Date/Time** (required)
     - When students can begin the exam
   - **Schedule End Date/Time** (required)
     - When the exam closes

3. Click **"Save"** or **"Create Exam"**

**Note**: The exam is saved as a **draft** and is not visible to students yet.

---

#### Step 2: Organize Your Exam with Sections

Sections help organize your exam into logical parts (e.g., Part I, Part II).

**Creating a Section:**
1. Open your draft exam
2. Click **"Add Section"** or **"New Section"**
3. Enter:
   - **Section Title** (e.g., "Part I: Multiple Choice")
   - **Directions** (optional instructions for students)
4. Click **"Save Section"**

**Managing Sections:**
- **Edit**: Click the edit icon next to the section name
- **Delete**: Click the delete icon (only if no questions exist)
- **Reorder**: Use drag-and-drop or up/down arrows
- **Duplicate**: Copy a section with all its questions

---

#### Step 3: Add Questions

The system supports five question types:

##### 1. Multiple Choice Questions (MCQ)

**How to Create:**
1. Select the section for the question
2. Click **"Add Question"**
3. Choose **"Multiple Choice"** as question type
4. Enter:
   - **Question Text**
   - **Options** (A, B, C, D, etc.)
   - **Correct Answer** (select the option)
   - **Points Awarded** (minimum 1)
5. Click **"Save Question"**

**Example:**
```
Question: What is the capital of France?
Options:
  A. London
  B. Paris
  C. Berlin
  D. Madrid
Correct Answer: B
Points: 2
```

---

##### 2. True/False Questions (TORF)

**How to Create:**
1. Select the section
2. Choose **"True/False"** as question type
3. Enter:
   - **Question Text**
   - **Correct Answer** (True or False)
   - **Points Awarded**
4. Click **"Save Question"**

**Example:**
```
Question: The Earth revolves around the Sun.
Correct Answer: True
Points: 1
```

---

##### 3. Enumeration Questions (ENUM)

Use enumeration questions when students need to list multiple items.

**Two Types:**
- **Ordered**: Specific sequence required (e.g., steps in a process)
- **Unordered**: Any order is acceptable (e.g., list of elements)

**How to Create:**
1. Select the section
2. Choose **"Enumeration"** as question type
3. Enter:
   - **Question Text**
   - **Enumeration Type** (Ordered/Unordered)
   - **Items** (the list of correct items)
   - **Points Awarded**
4. Click **"Save Question"**

**Example (Ordered):**
```
Question: List the steps of the scientific method in order.
Type: Ordered
Expected Items:
  1. Observation
  2. Question
  3. Hypothesis
  4. Experiment
  5. Analysis
  6. Conclusion
Points: 5
```

**Example (Unordered):**
```
Question: List 4 types of renewable energy.
Type: Unordered
Expected Items:
  - Solar
  - Wind
  - Hydro
  - Geothermal
Points: 4
```

---

##### 4. Identification Questions (IDEN)

Short answer questions requiring a specific answer.

**How to Create:**
1. Select the section
2. Choose **"Identification"** as question type
3. Enter:
   - **Question Text**
   - **Expected Answer**
   - **Points Awarded**
4. Click **"Save Question"**

**Example:**
```
Question: Who invented the telephone?
Expected Answer: Alexander Graham Bell
Points: 2
```

---

##### 5. Essay Questions (ESSAY)

Open-ended questions requiring paragraph responses.

**How to Create:**
1. Select the section
2. Choose **"Essay"** as question type
3. Enter:
   - **Question Text**
   - **Expected Answer/Rubric** (optional guidelines)
   - **Points Awarded**
4. Click **"Save Question"**

**Example:**
```
Question: Discuss the impact of social media on modern communication.
Expected Answer: [Guidelines for grading]
- Should mention at least 3 impacts
- Requires examples
- Minimum 200 words
Points: 10
```

**Note**: Essay questions require manual grading.

---

#### Step 4: Managing Questions

**Edit a Question:**
1. Click the edit icon next to the question
2. Make your changes
3. Click **"Save"**

**Delete a Question:**
1. Click the delete icon
2. Confirm deletion
3. Total points and item count automatically update

**Duplicate a Question:**
1. Click the duplicate icon
2. Edit the copy as needed

**Reorder Questions:**
- **Drag-and-drop**: Click and hold, then drag to new position
- **Up/Down buttons**: Click arrows to move one position

**Note**: Question order determines how students see them.

---

### Collaborating with Other Instructors

#### Adding Collaborators

1. Open your draft exam
2. Click **"Manage Collaborators"** or **"Add Collaborator"**
3. Search for instructor by name
4. Select the instructor from results
5. Click **"Add"**

**What Collaborators Can Do:**
- Edit exam questions and content
- Add/remove questions and sections
- View exam details
- Cannot submit for approval (only owner can)
- Cannot remove other collaborators

#### Removing Collaborators

1. Go to **"Manage Collaborators"**
2. Click the remove icon next to collaborator name
3. Confirm removal

**Note**: You cannot remove the exam owner. Collaborators can only be managed while the exam is in **draft** status.

---

### Previewing and Downloading Your Exam

#### Preview Your Exam

1. Open your draft exam
2. Click **"Preview"** button
3. View the exam as students will see it
4. Check formatting, question order, and instructions

#### Download as PDF

1. Open your draft exam
2. Click **"Download PDF"**
3. PDF file downloads to your device
4. Use for printing or archiving

#### Download as Word Document

1. Open your draft exam
2. Click **"Download Word"** or **"Download DOC"**
3. Word file downloads to your device
4. Edit offline if needed

**Note**: Downloads are available only for **draft** exams.

---

### Submitting Your Exam for Approval

#### Before Submitting - Checklist

Ensure your exam has:
- [ ] At least one section
- [ ] At least one question
- [ ] All questions have points assigned
- [ ] Correct answers are set for objective questions
- [ ] All required classes are assigned
- [ ] Valid schedule dates and duration
- [ ] Clear instructions in sections (if needed)

#### How to Submit

1. Open your draft exam
2. Review all content carefully
3. Click **"Submit for Approval"** button
4. Confirm submission

**What Happens Next:**
- Exam status changes to **"For Approval"**
- You can no longer edit the exam
- Program Chair receives notification
- You'll receive notification when reviewed

#### If Revisions Are Requested

If the Program Chair requests changes:

1. You'll receive a notification with revision notes
2. Exam returns to **"Draft"** status
3. Review the revision notes carefully
4. Make the requested changes
5. Submit for approval again

**Example Revision Notes:**
```
"Please add 2 more questions to Part II and
review the wording of question 5 for clarity."
```

---

### Viewing Exam Results and Analytics

#### Accessing Statistics

1. Navigate to **"Exam Statistics"** or **"Analytics"**
2. Select the exam you want to analyze
3. View comprehensive statistics

**Note**: Statistics are only available for **approved** exams.

---

#### Overall Statistics

**Key Metrics Displayed:**

1. **Total Students Enrolled**
   - Total number of students in all assigned classes

2. **Students Submitted**
   - Number of students who completed the exam

3. **Completion Rate**
   - Percentage of students who submitted
   - Formula: (Submitted / Total) × 100

4. **Highest Score**
   - Best performance on the exam

5. **Lowest Score**
   - Lowest performance on the exam

6. **Average Completion Time**
   - Average time students took to complete

**Example Display:**
```
Total Students: 45
Submitted: 38
Completion Rate: 84%
Highest Score: 98/100
Lowest Score: 42/100
Average Time: 1h 23m
```

---

#### Top Performers

View the students with the highest scores:

- Shows top 3 distinct score levels
- Displays student name, class, and score
- Includes all students who achieved top scores
- Sorted by score (highest first)

**Example:**
```
Top Performers:
1. Maria Santos (4-A) - 98/100
2. Juan Dela Cruz (4-B) - 98/100
3. Anna Rodriguez (4-A) - 95/100
4. Pedro Gonzales (4-C) - 95/100
```

---

#### Question Difficulty Analysis

##### Hardest Question
- Question with the lowest success rate
- Shows question number, text, and statistics
- Indicates how many students answered incorrectly

**Example:**
```
Hardest Question:
Question 12: "What is the capital of Australia?"
Wrong Answers: 28 students
Success Rate: 38%
```

##### Easiest Question
- Question with the highest success rate
- Shows question number, text, and statistics
- Indicates how many students answered correctly

**Example:**
```
Easiest Question:
Question 3: "2 + 2 = ?"
Correct Answers: 37 students
Success Rate: 97%
```

---

#### Detailed Question Statistics

For each question, view:

1. **Question Number and Text**
2. **Question Type** (MCQ, True/False, etc.)
3. **Points Awarded**
4. **Total Responses**
5. **Correct vs. Wrong Count**
6. **Success Rate Percentage**

**For Multiple Choice Questions:**
- See how many students chose each option
- View percentage for each option
- Correct answer is highlighted

**Example:**
```
Question 5: "What is photosynthesis?"
Type: Multiple Choice
Points: 5
Total Responses: 42

Response Breakdown:
A. "Process of cell division" - 5 students (11.9%)
B. "Process plants use to make food" - 35 students (83.3%) ✓
C. "Process of protein synthesis" - 2 students (4.8%)
D. "Process of respiration" - 0 students (0%)

Success Rate: 83.3%
```

---

#### Filtering by Class

To view statistics for a specific class:

1. Click **"Filter by Class"** dropdown
2. Select the class
3. All statistics recalculate for that class only
4. Select **"All Classes"** to view combined data

**Use Cases:**
- Compare performance between classes
- Identify which class needs more support
- Analyze class-specific trends

---

### Managing Your Exams

#### Exam Statuses

Your exams progress through these statuses:

1. **Draft**
   - Being created/edited
   - Not visible to students
   - Fully editable

2. **For Approval**
   - Submitted to Program Chair
   - Read-only (cannot edit)
   - Awaiting review

3. **Approved**
   - Approved by Program Chair
   - Visible to students based on schedule
   - Cannot edit content

4. **Ongoing**
   - Currently active (between start and end time)
   - Students can take the exam
   - Cannot modify

5. **Archived**
   - Schedule has ended
   - Read-only
   - Available for reference and statistics

---

#### Duplicating an Exam

To create a copy of an existing exam:

1. Find the exam you want to copy
2. Click **"Duplicate"** or **"Copy"** icon
3. New draft exam is created with:
   - All questions and sections
   - Same structure and content
   - New title: "[Original Title] - Copy"
4. Edit the duplicate as needed
5. Submit separately for approval

**Use Cases:**
- Create similar exams for different terms
- Build on previous exam formats
- Make variations for different classes

---

#### Deleting an Exam

1. Find the exam (must be in **Draft** status)
2. Click **"Delete"** icon
3. Confirm deletion

**Important Notes:**
- Only **draft** exams can be deleted
- Approved, ongoing, or archived exams cannot be deleted
- Deletion is permanent and cannot be undone
- Consider archiving instead of deleting

---

### Notifications

#### Viewing Notifications

1. Click the notification bell icon
2. View unread count badge
3. Click to see notification list

#### Types of Notifications

**Exam Approved:**
```
Your exam "Midterm Math" has been approved!
Schedule: Oct 28, 2024, 9:00 AM - 11:00 AM
Duration: 120 minutes
Password: ABC123 (if set)
```

**Revision Requested:**
```
Exam "Final Science" needs revisions
Program Chair notes:
"Please add more questions to Part II and
review grammar in question 5."
```

#### Managing Notifications

- **Mark as Read**: Click individual notification
- **Mark All as Read**: Click "Mark all read" button
- **Delete**: Click delete icon on notification
- **Filter**: View read/unread notifications

---

### Best Practices for Instructors

#### Exam Design

1. **Clear Instructions**
   - Provide directions for each section
   - Explain any special requirements
   - State allowed materials/resources

2. **Balanced Difficulty**
   - Mix easy, medium, and hard questions
   - Start with easier questions
   - Place harder questions in the middle

3. **Point Distribution**
   - Allocate points based on difficulty
   - Ensure total points align with grading scale
   - Consider partial credit for complex questions

4. **Question Quality**
   - Avoid ambiguous wording
   - Use clear, concise language
   - Review all questions before submitting
   - Have a colleague review if possible

5. **Time Management**
   - Allow adequate time per question
   - General rule: 1-2 minutes per multiple choice
   - More time for essay and problem-solving

#### Collaboration Tips

1. **Divide Work Clearly**
   - Assign sections to specific instructors
   - Communicate via other channels
   - Set deadlines for contributions

2. **Review Together**
   - Schedule a review meeting
   - Check for consistency in difficulty
   - Ensure unified grading standards

3. **Version Control**
   - Make one person responsible for final review
   - Avoid simultaneous editing of same section
   - Use clear section/question titles

#### Before Submission

1. **Content Review**
   - [ ] All questions have correct answers
   - [ ] No spelling/grammar errors
   - [ ] Instructions are clear
   - [ ] Point values are appropriate

2. **Technical Check**
   - [ ] All classes are assigned
   - [ ] Duration is reasonable
   - [ ] Schedule dates are correct
   - [ ] Sections are in logical order

3. **Preview**
   - [ ] Use Preview function
   - [ ] Check student view
   - [ ] Verify question formatting
   - [ ] Test any special formatting

---

### Troubleshooting Common Issues

#### "Cannot submit for approval"

**Possible Causes:**
- Exam has no questions
- Exam has no sections
- No classes assigned
- Invalid schedule dates
- Missing required fields

**Solution:**
- Review error message
- Complete all required information
- Add at least one question
- Check schedule dates are in future

---

#### "Cannot edit exam"

**Possible Causes:**
- Exam is not in draft status
- Exam is submitted for approval
- Exam is approved or archived
- You are not the owner or collaborator

**Solution:**
- Check exam status
- If "For Approval": wait for review or contact Program Chair
- If "Approved": cannot edit content
- If needed, ask Program Chair to rescind approval

---

#### "Collaborator cannot be added"

**Possible Causes:**
- Exam is not in draft status
- Instructor does not exist
- Instructor already added
- System error

**Solution:**
- Verify exam is in draft status
- Check instructor name spelling
- Refresh page and try again
- Contact admin if issue persists

---

#### "Statistics not showing"

**Possible Causes:**
- Exam is not approved
- No students have taken exam
- Exam schedule hasn't started
- Browser cache issue

**Solution:**
- Verify exam status is "Approved" or "Ongoing"
- Wait for students to submit attempts
- Refresh browser page
- Clear browser cache

---

## Program Chair Guide

### Role Overview

As a Program Chair, you are responsible for:
- Reviewing exams submitted by instructors
- Approving or requesting revisions
- Setting final exam schedules and passwords
- Maintaining exam quality standards
- Managing the approval workflow

**You cannot:**
- Create exams
- Edit exam questions or content
- Delete exams
- Grade student work

---

### Accessing the Approval Dashboard

1. Log in with your Program Chair credentials
2. Navigate to **"Manage Approval"** or **"Exam Approval"**
3. View list of exams pending approval

---

### Reviewing Exams

#### Viewing Exam List

The approval dashboard shows:
- **Exam Title**
- **Instructor Name**
- **Subject**
- **Status** (For Approval, Approved)
- **Submission Date**
- **Current Approval Status**

**Filter Options:**
- Search by title
- Filter by subject
- Filter by instructor
- Filter by status

---

#### Viewing Exam Details

To review an exam:

1. Click on the exam title or **"View Details"**
2. Review the following information:

**Basic Information:**
- Exam title and description
- Subject and assigned classes
- Created by instructor
- Collaborators (if any)
- Proposed schedule and duration

**Exam Content:**
- All sections with directions
- All questions with:
  - Question text
  - Question type
  - Points awarded
  - Correct answers (for verification)
  - Options (for MCQ)

**Statistics:**
- Total number of items
- Total points possible
- Number of sections
- Question type breakdown

**Approval History:**
- Previous approval attempts
- Previous revision notes
- Date of submissions

---

### Approving an Exam

When the exam meets quality standards:

#### Step 1: Review Thoroughly

Check for:
- [ ] Clear, unambiguous questions
- [ ] Correct grammar and spelling
- [ ] Appropriate difficulty level
- [ ] Balanced point distribution
- [ ] Correct answers are accurate
- [ ] Adequate number of questions
- [ ] Clear section instructions
- [ ] Alignment with curriculum

#### Step 2: Set Exam Parameters

1. Click **"Approve Exam"** button
2. Fill in the approval form:

**Required Fields:**

- **Duration** (in minutes)
  - Example: 120 for 2 hours
  - Should allow adequate time for all questions

- **Schedule Start Date/Time**
  - When students can begin the exam
  - Must be in the future
  - Example: "2024-10-28 09:00 AM"

- **Schedule End Date/Time**
  - When the exam closes
  - Must be after start date
  - Example: "2024-10-28 11:00 AM"

**Optional Fields:**

- **Exam Password/OTP**
  - Optional security code
  - Students must enter to access exam
  - Example: "MATH2024"
  - Leave blank if no password needed

- **Notes**
  - Any comments for the instructor
  - Example: "Excellent exam design!"

#### Step 3: Confirm Approval

1. Review your settings
2. Click **"Approve"** or **"Confirm Approval"**
3. System processes approval

**What Happens:**
- Exam status changes to **"Approved"**
- Exam becomes visible to students based on schedule
- Instructor receives approval notification with:
  - Confirmation message
  - Schedule details
  - Password (if set)
- Approval record is created in system

**Important:** Once approved, the exam content cannot be edited by anyone.

---

### Requesting Revisions

If the exam needs improvements:

#### Step 1: Identify Issues

Common reasons for requesting revisions:
- Unclear or ambiguous questions
- Grammatical errors
- Insufficient number of questions
- Incorrect answers
- Inappropriate difficulty
- Missing instructions
- Formatting issues
- Content not aligned with curriculum

#### Step 2: Request Revisions

1. Click **"Request Revisions"** or **"Reject"** button
2. Fill in revision notes:

**Required:**
- **Detailed Notes** (minimum 10 characters)
  - Be specific about what needs changing
  - Reference specific questions if possible
  - Provide constructive feedback

**Example Good Notes:**
```
Please make the following changes:

1. Part I: Add 3 more multiple choice questions
2. Question 5: The wording is unclear. Rephrase to be more specific.
3. Question 12: Correct answer should be 'B', not 'C'
4. Part II: Add directions for enumeration questions
5. Overall: Check for spelling errors in questions 7, 9, and 15
```

**Example Poor Notes:**
```
"Needs improvement" ❌
"Add more questions" ❌
"Fix errors" ❌
```

#### Step 3: Submit Revision Request

1. Review your notes
2. Click **"Submit"** or **"Request Revision"**

**What Happens:**
- Exam returns to **"Draft"** status
- Instructor can edit the exam again
- Instructor receives notification with your notes
- Revision request logged in approval history
- Instructor must resubmit after making changes

---

### Rescinding Approval

If an already-approved exam needs to be withdrawn:

**Common Reasons:**
- Curriculum change
- Error discovered after approval
- Schedule conflict
- Policy change

**How to Rescind:**

1. Find the approved exam
2. Click **"Rescind Approval"** or **"Withdraw Approval"**
3. Enter reason (optional but recommended):
```
Example: "Course schedule changed. Please adjust
exam to align with new unit sequence."
```
4. Confirm rescission

**What Happens:**
- Exam status changes to **"Draft"**
- Exam is no longer visible to students
- Instructor can edit exam again
- Instructor receives notification
- Instructor must resubmit for approval

**Important:** Only rescind if absolutely necessary. This disrupts the exam schedule.

---

### Managing Approval Workflow

#### Approval Queue

**Prioritizing Reviews:**

1. **Urgent Exams**
   - Exams with near-term proposed schedules
   - Mark as priority

2. **Regular Reviews**
   - Review in order of submission
   - Aim for 24-48 hour turnaround

3. **Follow-ups**
   - Track resubmitted exams
   - Review revision compliance

#### Tracking Approval History

For each exam, view:
- All previous submissions
- All revision requests
- All approval attempts
- Date and time stamps
- Your previous notes

**Use Cases:**
- Verify instructor made requested changes
- Review recurring issues
- Track instructor improvement
- Maintain audit trail

---

### Best Practices for Program Chairs

#### Review Standards

1. **Content Quality**
   - Questions align with curriculum
   - Appropriate difficulty for level
   - Clear and unambiguous wording
   - Correct answers are accurate

2. **Exam Structure**
   - Logical section organization
   - Clear instructions provided
   - Balanced point distribution
   - Adequate number of questions

3. **Technical Requirements**
   - Valid schedule dates
   - Appropriate duration
   - Correct class assignments
   - No system errors

#### Communication

1. **Revision Notes**
   - Be specific and detailed
   - Use constructive language
   - Reference question numbers
   - Provide examples when helpful
   - Suggest improvements, not just problems

2. **Approval Comments**
   - Acknowledge good work
   - Note exemplary practices
   - Encourage quality

#### Time Management

1. **Set Review Schedule**
   - Check approval queue daily
   - Set aside dedicated review time
   - Prioritize by exam schedule

2. **Response Time**
   - Aim for 24-48 hour turnaround
   - Communicate delays if needed
   - Fast-track urgent exams

#### Quality Assurance

1. **Consistency**
   - Apply same standards to all exams
   - Create internal rubric if helpful
   - Document common issues

2. **Documentation**
   - Keep detailed revision notes
   - Track common instructor errors
   - Share feedback with instructors

3. **Continuous Improvement**
   - Meet with instructors about recurring issues
   - Provide training on common problems
   - Share best practices

---

### Troubleshooting Common Issues

#### "Cannot approve exam"

**Possible Causes:**
- Exam is not in "For Approval" status
- Required fields missing
- Schedule dates are invalid
- System error

**Solution:**
- Verify exam status
- Check all required fields are filled
- Ensure end date is after start date
- Refresh and try again

---

#### "Cannot see exam content"

**Possible Causes:**
- Page not fully loaded
- Browser compatibility issue
- Permissions error

**Solution:**
- Refresh the page
- Try different browser
- Contact admin if persists

---

#### "Revision notes not saved"

**Possible Causes:**
- Notes too short (minimum 10 characters)
- Session timeout
- Network error

**Solution:**
- Ensure notes are detailed enough
- Copy notes before submitting
- Resubmit if error occurs

---

## Admin Guide

### Role Overview

As an Administrator, you manage:
- Users (instructors, students, program chairs)
- Classes and sections
- Subjects and courses
- System-wide settings
- User imports and enrollments

**You cannot:**
- Create or edit exam content
- Approve exams (unless also a Program Chair)
- Take exams

---

### Managing Users

#### Adding New Users

**Add Instructor:**
1. Navigate to **"Users"** > **"Instructors"**
2. Click **"Add Instructor"**
3. Fill in required information:
   - Employee ID
   - First Name
   - Last Name
   - Email Address
   - Initial Password
   - Department (optional)
4. Click **"Save"**

**Add Student:**
1. Navigate to **"Users"** > **"Students"**
2. Click **"Add Student"**
3. Fill in required information:
   - Student ID
   - First Name
   - Last Name
   - Email Address
   - Initial Password
   - Enrolled Class(es)
4. Click **"Save"**

**Add Program Chair:**
1. Navigate to **"Users"** > **"Program Chairs"**
2. Click **"Add Program Chair"**
3. Fill in required information:
   - Employee ID
   - First Name
   - Last Name
   - Email Address
   - Initial Password
   - Department
4. Click **"Save"**

**Add Admin:**
1. Navigate to **"Users"** > **"Admins"**
2. Click **"Add Admin"**
3. Fill in required information
4. Click **"Save"**

**Note:** Users receive an email with their login credentials (if email is configured).

---

#### Importing Users via CSV

For bulk user creation:

**Step 1: Prepare CSV File**

**Instructor CSV Format:**
```csv
employee_id,first_name,last_name,email,password,department
EMP001,John,Doe,john.doe@school.edu,password123,Mathematics
EMP002,Jane,Smith,jane.smith@school.edu,password123,Science
```

**Student CSV Format:**
```csv
student_id,first_name,last_name,email,password,class_id
2024001,Maria,Santos,maria@school.edu,password123,1
2024002,Juan,Cruz,juan@school.edu,password123,1
```

**Step 2: Import File**
1. Navigate to **"Import Users"**
2. Select user type (Instructor/Student)
3. Click **"Choose File"**
4. Select your CSV file
5. Click **"Import"**

**Step 3: Review Results**
- System displays import summary
- Shows successful imports
- Lists any errors with reasons
- Download error log if needed

---

#### Managing Existing Users

**View User List:**
- Search by name, ID, or email
- Filter by user type
- Sort by any column

**Edit User:**
1. Click edit icon next to user
2. Modify information
3. Click **"Save"**

**Reset Password:**
1. Click **"Reset Password"** on user
2. Enter new password
3. Confirm password
4. Click **"Save"**
5. Notify user of new password

**Deactivate User:**
1. Click **"Deactivate"** or **"Disable"**
2. Confirm action
3. User cannot log in (but data preserved)

**Reactivate User:**
1. Filter to show inactive users
2. Click **"Reactivate"**
3. Confirm action

**Delete User:**
1. Click **"Delete"** icon
2. Confirm deletion
3. **Warning:** This permanently removes user data

---

### Managing Classes

#### Creating Classes

1. Navigate to **"Classes"** or **"Manage Classes"**
2. Click **"Add Class"**
3. Fill in information:
   - **Class Name** (e.g., "4-A", "Grade 10 - Section B")
   - **Description** (optional)
   - **Academic Year** (e.g., "2024-2025")
   - **Semester/Quarter**
4. Click **"Save"**

---

#### Managing Class Enrollments

**Enroll Students in Class:**
1. Open the class
2. Click **"Manage Students"** or **"Enroll"**
3. Search for students
4. Select students to enroll
5. Click **"Enroll Selected"**

**Remove Student from Class:**
1. Open the class
2. Find student in enrollment list
3. Click **"Remove"** or **"Unenroll"**
4. Confirm action

**Bulk Enrollment:**
- Use CSV import for multiple students
- Include class_id in student import file

---

#### Editing/Deleting Classes

**Edit Class:**
1. Click edit icon next to class
2. Modify information
3. Click **"Save"**

**Delete Class:**
1. Click delete icon
2. Confirm deletion
3. **Warning:** Cannot delete class with:
   - Enrolled students
   - Assigned exams
   - Must reassign first

---

### Managing Subjects

#### Adding Subjects

1. Navigate to **"Subjects"** or **"Manage Subjects"**
2. Click **"Add Subject"**
3. Fill in information:
   - **Subject Code** (e.g., "MATH101")
   - **Subject Name** (e.g., "College Algebra")
   - **Description** (optional)
   - **Department** (optional)
4. Click **"Save"**

**Subject Usage:**
- Subjects are assigned to exams
- Used for organizing and filtering
- Helps with reporting and analytics

---

#### Editing/Deleting Subjects

**Edit Subject:**
1. Click edit icon
2. Modify information
3. Click **"Save"**

**Delete Subject:**
1. Click delete icon
2. Confirm deletion
3. **Warning:** Cannot delete if:
   - Exams assigned to subject
   - Must reassign exams first

---

### System Monitoring

#### Dashboard Overview

View system-wide statistics:
- Total users by type
- Total active exams
- Total classes
- Recent activity
- System health status

---

#### Reports

**User Reports:**
- Total users by type
- Active/inactive users
- Recent registrations

**Exam Reports:**
- Total exams by status
- Exams by subject
- Upcoming scheduled exams
- Recently completed exams

**Performance Reports:**
- System usage statistics
- Peak usage times
- Storage usage

---

### Best Practices for Admins

#### User Management

1. **Password Security**
   - Use strong initial passwords
   - Require password change on first login
   - Regularly remind users to update passwords

2. **Regular Audits**
   - Review user accounts quarterly
   - Deactivate unused accounts
   - Verify enrollments are current

3. **Data Backup**
   - Maintain regular backups
   - Test restore procedures
   - Document backup schedule

#### Organization

1. **Naming Conventions**
   - Use consistent class naming
   - Standardize subject codes
   - Document conventions for team

2. **Academic Year Management**
   - Archive old classes at year end
   - Create new classes before term starts
   - Update enrollments promptly

3. **Communication**
   - Notify users of system changes
   - Provide user guides and training
   - Maintain help documentation

---

### Troubleshooting Common Issues

#### "Cannot import CSV"

**Possible Causes:**
- File format incorrect
- Missing required columns
- Invalid data in cells
- File too large

**Solution:**
- Verify CSV format matches template
- Check all required columns present
- Validate data (IDs, emails)
- Split large files into smaller batches

---

#### "User cannot log in"

**Possible Causes:**
- Account deactivated
- Password incorrect
- User not in system
- Session expired

**Solution:**
- Verify account is active
- Reset password
- Check user exists in system
- Clear browser cookies/cache

---

#### "Class enrollment error"

**Possible Causes:**
- Student already enrolled
- Class does not exist
- Invalid student ID

**Solution:**
- Check current enrollments
- Verify class exists
- Validate student ID
- Refresh and try again

---

## Frequently Asked Questions

### For Instructors

**Q: Can I edit an exam after submitting for approval?**
A: No, once submitted, the exam is locked. If you need changes, contact the Program Chair to rescind approval or wait for revision request.

**Q: How long does approval take?**
A: Typically 24-48 hours. Contact the Program Chair if urgent.

**Q: Can I see who took my exam?**
A: Yes, in the Statistics section you can see all students who submitted attempts.

**Q: Can I delete a question after students have taken the exam?**
A: No, approved and ongoing exams cannot be modified. Plan carefully before submitting.

**Q: How do I grade essay questions?**
A: Essay questions require manual grading through the results interface (feature may vary by implementation).

**Q: Can I extend the exam time for a student?**
A: Contact your administrator for special accommodations.

**Q: What happens if a student doesn't finish in time?**
A: The exam auto-submits when the end time is reached, submitting whatever answers the student completed.

---

### For Program Chairs

**Q: Can I edit exam questions before approving?**
A: No, you can only approve or request revisions. Instructors must make content changes.

**Q: What if I approved an exam by mistake?**
A: Use the "Rescind Approval" function to return it to draft status.

**Q: Can I change the exam schedule after approval?**
A: Yes, you can rescind and re-approve with new schedule, but this disrupts student access.

**Q: How do I handle urgent exam approvals?**
A: Prioritize them in your queue and notify the instructor when complete.

**Q: Can I see exam statistics?**
A: This depends on your system permissions. Typically only instructors and admins can view detailed statistics.

---

### For Admins

**Q: Can I approve exams?**
A: Only if you also have Program Chair role. Admin role alone cannot approve exams.

**Q: How do I reset a user's password?**
A: Use the "Reset Password" function in the user management section.

**Q: Can I delete a class with enrolled students?**
A: No, you must first remove or reassign all students.

**Q: How do I back up exam data?**
A: Contact your system administrator for database backup procedures.

**Q: Can I create exams?**
A: Only if you also have Instructor role. Admin role alone cannot create exams.

---

### General Questions

**Q: What browsers are supported?**
A: Modern versions of Chrome, Firefox, Safari, and Edge. Chrome is recommended for best experience.

**Q: Is there a mobile app?**
A: Yes, students can take exams via mobile API. Instructor and admin functions are web-based.

**Q: How is data secured?**
A: The system uses authentication, role-based access control, and secure connections. Contact your IT department for specific security details.

**Q: Can exams be printed?**
A: Yes, instructors can download exams as PDF or Word documents while in draft status.

**Q: What if I encounter a bug?**
A: Report it to your system administrator with details about what happened and when.

**Q: Is there training available?**
A: Contact your institution's training coordinator or IT support for available training sessions.

---

## Getting Help

### Technical Support

**For System Issues:**
- Contact your IT department
- Provide error messages
- Include screenshots if helpful
- Note date/time of issue

**For Account Issues:**
- Contact your administrator
- Verify your user role
- Request password reset if needed

### Training and Resources

**User Guides:**
- Refer to this manual
- Check for video tutorials (if available)
- Attend training sessions

**Best Practices:**
- Review relevant sections of this guide
- Consult with experienced users
- Share tips with colleagues

---

## Appendix

### Keyboard Shortcuts

(If implemented in your system)

- `Ctrl + S` - Save current work
- `Ctrl + P` - Preview exam
- `Esc` - Close modal/dialog

### Glossary

**Terms Used in System:**

- **Attempt**: A student's instance of taking an exam
- **Collaboration**: Multiple instructors working on one exam
- **Draft**: Exam being created, not yet submitted
- **Enumeration**: List-type question (ordered or unordered)
- **OTP**: One-Time Password (exam password)
- **Rescind**: Withdraw approval of exam
- **Section**: Logical division of exam (e.g., Part I, Part II)
- **Submission**: When student completes and turns in exam
- **Revision**: Changes requested by Program Chair

### System Limits

**Exams:**
- Maximum title length: 200 characters
- Minimum questions: 1
- Maximum questions: No hard limit (consider performance)
- Maximum points per question: No hard limit

**Questions:**
- Maximum question text: 65,535 characters (text field)
- Maximum options for MCQ: Recommended 5-8
- Minimum points: 1

**Scheduling:**
- Schedule must be in future (for new exams)
- End time must be after start time
- Duration in minutes (positive integer)

---

**Document Version:** 1.0
**Last Updated:** October 27, 2025
**System:** Exam-In-Ease Platform

---

## Quick Reference Cards

### Instructor Quick Reference

**Create Exam Workflow:**
1. Create Exam → Add Sections → Add Questions
2. Preview → Submit for Approval
3. Wait for Review → Address Revisions if needed
4. View Statistics after students complete

**Exam Statuses:**
- Draft → For Approval → Approved → Ongoing → Archived

**Question Types:**
- MCQ, True/False, Enumeration, Identification, Essay

---

### Program Chair Quick Reference

**Approval Workflow:**
1. View Exam Details → Review Content
2. Approve (set schedule) OR Request Revisions
3. Track in Approval History

**Actions Available:**
- Approve Exam
- Request Revision
- Rescind Approval

---

### Admin Quick Reference

**Main Responsibilities:**
- Manage Users (Add/Edit/Import)
- Manage Classes and Subjects
- Handle Enrollments
- System Monitoring

**Cannot Do:**
- Create/Edit Exams
- Approve Exams
- Grade Exams

---

*For additional assistance, contact your system administrator or IT support team.*
