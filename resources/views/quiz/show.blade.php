<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- �R���g���[���[����n���ꂽ�v�̃^�C�g����\�� --}}
            {{ $tile->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- �܂��N�C�Y�f�[�^���Ȃ��ꍇ�̕\�� --}}
                    @if($questions->isEmpty())
                        <p>���̃R�[�X�ɂ͂܂��N�C�Y������܂���B</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-blue-500 hover:underline">
                            &laquo; �_�b�V���{�[�h�ɖ߂�
                        </a>
                    @else
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold">���ꗗ</h3>
                            {{-- �N�C�Y�J�n�{�^�� --}}
                            <a href="{{ route('quiz.start') }}" class="inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                                �w�K���J�n����
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            {{-- �������[�v�ŕ\�� --}}
                            @foreach($questions as $question)
                                <div class="p-4 border rounded-lg">
                                    <p>{{ $question->question_text }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>