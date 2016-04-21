Hi {{$user->firstname}}! You have been invited to join a new course on Mango!


You can join the course `{{$course->name}}` by using the link below:

<a href="http://localhost:8888/#!/courses/{{$course->id}}/new/{{$inviteTtoken}}">Join Class</a>