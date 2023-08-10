<form method="post" action="{{route('register')}}">
    @csrf
    <input name="name" type="text">
    <input name="email" type="email">
    <input name="password" type="password">
    <input type="submit" value="send">
</form>
