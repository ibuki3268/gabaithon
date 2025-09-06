<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            �N�C�Y
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">

                    <h3 class="text-2xl font-bold mb-6">��肪������܂���ł���</h3>
                    <p class="mb-6">
                        ���̃R�[�X�ɂ́A���ݗ��p�ł���N�C�Y������܂���B<br>
                        �ʂ̃R�[�X���������A��ł�����x���������������B
                    </p>
                    <a href="{{ route('dashboard') }}"
                       class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                        �_�b�V���{�[�h�ɖ߂�
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>