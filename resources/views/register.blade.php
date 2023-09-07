<style>
    input{
        display: block;
        margin: 10px auto;
        padding: 3px;
        width: 100%;
    }
    label{
        width: 100%;
    }
    form{
        padding: 3rem;
        border: 2px solid seagreen;
        border-radius: 10px;
        width: 200px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: flex-start;
    }
    body{
        display: flex;
        align-items: center;
    }
</style>
{{--<form  method="post" action="http://127.0.0.1:8000/api/user/login" enctype="multipart/form-data">    @csrf--}}
{{--    {{csrf_field()}}--}}
{{--    <label>--}}
{{--        email:--}}
{{--        <input type="email" name="email" id="email">--}}
{{--    </label>--}}
{{--    <label>--}}
{{--        password:--}}
{{--        <input type="password" name="password" id="password">--}}
{{--    </label>--}}
{{--    <input type="file" name="video">--}}
{{--    <input type="submit" value="login">--}}
    <button>Submit</button>

{{--    <label>--}}
{{--        comment on:--}}
{{--        <input type="text" name="comment_id">--}}
{{--    </label>--}}
{{--    <label>--}}
{{--        comment:--}}
{{--        <input type="text" name="content">--}}
{{--    </label>--}}
{{--    <label>--}}
{{--        comment from:--}}
{{--        <input type="text" name="user_id">--}}
{{--    </label>--}}
{{--    <input type="submit" value="upload">--}}
{{--</form>--}}
<script src="{{asset('jquery.min.js')}}"></script>
<script>
    $(function (){
        $token = '';
        $('button').on('click',function (){
            $.ajax({
                method: 'POST',
                url: 'http://127.0.0.1:8000/api/user/login',
                data : {
                    email: 'admin@morty.net',
                    password: '00225588'
                    // email: $('#email').value(),
                    // password : $('#password').value()
                },
                success: function (data, status, xml){
                    if(data['status'] === 200){
                        // $token = data['data']['token'];
                        console.log(data)
                    }
                    if(data['status'] === 401){
                        console.log('Can not Enter');
                    }
                }
            })
        });
    });
</script>
