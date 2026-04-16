<div class="mb-3">
    <label for="user_id" class="form-label">Пользователь</label>
    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
        <option value="">Выберите пользователя</option>
        @foreach($users as $selectUser)
            <option value="{{ $selectUser->id }}"
                @selected((string) old('user_id', $order?->user_id ?? '') === (string) $selectUser->id)>
                {{ $selectUser->name }} ({{ $selectUser->email }})
            </option>
        @endforeach
    </select>
    @error('user_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">Статус</label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
        <option value="">Выберите статус</option>
        @foreach($statuses as $statusCode => $statusLabel)
            <option value="{{ $statusCode }}" @selected(old('status', $order?->status ?? '') === $statusCode)>
                {{ $statusLabel }}
            </option>
        @endforeach
    </select>
    @error('status')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="total" class="form-label">Сумма</label>
    <input
        type="number"
        step="0.01"
        min="0"
        id="total"
        name="total"
        value="{{ old('total', $order?->total ?? '') }}"
        class="form-control @error('total') is-invalid @enderror"
        required
    >
    @error('total')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
