<style>
    input{
        display: block;
        margin: 10px auto;
        padding: 3px;
    }
    form{
        padding: 3rem;
        border: 2px solid seagreen;
        width: 200px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: flex-start;
    }
</style>
<form  method="post" action="{{route('store',2)}}" enctype="multipart/form-data">
    @csrf
    {{csrf_field()}}
    <label>
        Title:
        <input type="text" name="title">
    </label>
    <label>
        Description:
        <input type="text" name="description">
    </label>
    <input type="file" name="video">
    <input type="submit" value="upload">

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
</form>
