<form action="{{ route('landlord.logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>
