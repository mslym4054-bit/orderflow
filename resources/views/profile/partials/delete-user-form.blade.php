<section class="space-y-4">
    <header>
        <h2 class="text-lg font-medium text-ink">حذف الحساب</h2>
        <p class="mt-1 text-sm text-muted">بمجرد حذف حسابك، كل بياناتك بتنحذف نهائيًا. حمّل أي بيانات بدك تحتفظ فيها قبل الحذف.</p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        حذف الحساب
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-ink">متأكد بدك تحذف حسابك؟</h2>
            <p class="mt-1 text-sm text-muted">أدخل كلمة المرور للتأكيد. هذا الإجراء نهائي.</p>

            <div class="mt-6">
                <x-input-label for="password" value="كلمة المرور" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="كلمة المرور" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close-modal')">إلغاء</x-secondary-button>
                <x-danger-button>حذف الحساب</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
