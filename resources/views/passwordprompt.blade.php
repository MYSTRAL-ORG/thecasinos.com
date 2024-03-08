<form method="POST" action="{{ url('password-check') }}">
    @csrf
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <button type="submit">Submit</button>
    @error('password')
    <div>{{ $message }}</div>
    @enderror
</form>
