###Login & Authentication

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/auth |  Post        |  email, password |Logs in user|JWT Auth Token|
| /v1/users | Post        |   firstname,lastname,email,password|Creates new user|Auth Token|
| /v1/passwordResetRequest| Post        |   email | Emails password reset token|"success", or error message|
| /v1/passwordResetResponse| Post        |   email,token,password|Updates user email given | "success", or error message|

Note that all API calls outside of the authentication section must have the Auth token provided as a url parameter named "token". The Auth token is returned on a successful login request. 

###Course Management
| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/courses| Post        |   name,section_name,type| Creates new course and section with the type and names given. Type must exist in the types table |"success", or error message|
| /v1/courses/sections| Post        |   course_id,section_name| Creates new section for the specified course.| "success" or error|
| /v1/sections| Delete      |   section_id| Deletes the specified section.| "success" or error|
| /v1/users/sections|Get|   | |Array of sections the user is enrolled in with associated courses/roles|
| /v1/users/sections|Post        |   email,sectionid| Invites user to course. Logged in user must have permission.|"success", or error message|
| /v1/users/sections/accept|Post        |   token| Accepts a section invite, adds user to section|"success", or error message|

###Announcements
Announcements are made to a particular section, not a course. Any user enrolled within the section can view the announcements, but only a TA or instructor can create a new announcement. 

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/announcements| Post        |   title,body, sectionID| Creates new announcement with the provided paramaters|"success", or error message|
| /v1/announcements/{section_id}| Get         | | | Array of announcements for the specified section, or error message|
| /v1/announcements/edit| Post         | sectionID,announcement_id  | Edits the corresponding announcement | "success" or error message|
| /v1/announcements/delete| Post         | sectionID,announcement_id  | Deletes the corresponding announcement | "success" or error message|


###Assignments
Announcements are specific to each section. Each assignment can optionally have a deadline. Deadlines should be provided in the format "Y-m-d H:i", for example "2016-01-11 7:58". Body attributes with a * are optional. 

If the quiz attribute is set to true, then the quiz data should be provided as json. 

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/sections/{section_id}/assignments| Get |   |  | Array of announcements for the specified section
| /v1/sections/{section_id}/assignments| Post | title,description,filesubmission(boolean),quiz(boolean),*data(json),category_id, *deadline  | Creates specified assignment | "success", or error message
| /v1/sections/{section_id}/updateAssignment| Post | title,description,filesubmission(boolean),quiz(boolean),*data(json),category_id, *deadline  | Updates specified assignment | "success", or error message
| /v1/sections/{section_id}/deleteAssignment| Post | id | Deletes specified quiz | "success", or error message


###Grades
Each assignment can have a grade entry for each student in the assignment's section. Each grade must be assigned a category. A default category worth 100% of the class is created for each section automatically; However, if a new category is created for that section the default category will become worth 0%. 

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/sections/{section_id}/grades| Get |   |  | Returns grades for the current user in the specified section
| /v1/assignments/{assignment_id}/grades| Get |   |  | Returns all grades for all user for specified assignment
| /v1/assignments/{assignment_id}/grades| Post|  score, user_id |  Creates a grade for the assignment/user given| "success" or error message
| /v1/assignments/{assignment_id}/updateGrade| Post|  score, grade_id |  Updates the grade for the given grade entry| "success" or error message
| /v1/assignments/{assignment_id}/deleteGrade| Post|  grade_id |  Deletes the grade for the given grade entry| "success" or error message


###Grade Categories
Each section can have multiple grade categories. These are useful for weighting or organizing assignments. All sections have a default category which can not be deleted or updated. Changing which assignments are in which categories is done through the Assignment endpoints, not the category endpoints.  

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/sections/{section_id}/categories| Get |   |  | Returns categories for the given section
| /v1/sections/{section_id}/categories/{category_id}/assignments| Get |   |  | Returns assignments inside this category
| v1/sections/{section_id}/categories| Post| name,weight  |  Creates given category | "success" or error message
| v1/sections/{section_id}/updateCategory| Post| id, name,weight  |  Updates given category | "success" or error message
| v1/sections/{section_id}/deleteCategory| Post| id  |  Deletes given category | "success" or error message


###Forum
Each course has its own forum space, but we still need to send the section ID that the user is in for authentication purposes.

| URI      |      Action  |  Body  | Result  | Response |
|----------|--------------|--------|--------|-------|
| /v1/forum/{section_id}/threads| Get |   |  | Returns all threads in a course forum space
| /v1/forum/{section_id}/threads/{thread_id}/posts| Get |   |  | Returns all posts in a thread
| v1/forum/threads| Post| section_id,title,body,anonymous,sticky  | Creates a thread | "success" or error message
| v1/forum/threads/update| Post| section_id,thread_id,title,body,anonymous,sticky  |  Updates a thread given a thread_id | "success" or error message
| v1/forum/threads/delete| Post| section_id,thread_id  |  Deletes given thread | "success" or error message
| v1/forum/threads/lock| Post| section_id,thread_id  |  Locks given thread | "success" or error message
| v1/forum/threads/unlock| Post| section_id,thread_id  |  Unlocks given thread | "success" or error message
| v1/forum/posts| Post| section_id,thread_id,body,anonymous,reply_id  |  Posts a comment to a thread_id | "success" or error message
| v1/forum/posts/update| Post| section_id,post_id,body,anonymous  |  Updates given post | "success" or error message
| v1/forum/posts/delete| Post| section_id,post_id  |  Deletes given post | "success" or error message
