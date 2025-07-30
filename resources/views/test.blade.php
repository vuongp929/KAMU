<div>
    <form action="{{ route('momo_payment') }}" method="POST">
        @csrf
        <button type="submit">thanh toán momo</button>
    </form>
</div>