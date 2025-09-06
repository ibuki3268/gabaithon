<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            �N�C�Y����
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-3xl font-bold text-center mb-4">���ʔ��\</h3>

                    <p class="text-xl text-center mb-8">
                        ���Ȃ��̃X�R�A��... <span class="text-4xl font-black text-blue-500">{{ $correctCount }} / {{ $total }}</span> �␳���ł��I
                    </p>

                    {{-- �e���̌��ʂ��ڍו\�� --}}
                    <div class="space-y-2 mb-8">
                        @foreach($questions as $index => $question)
                            <div class="p-3 rounded-lg {{ $score[$index] ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <p class="font-bold">{{ $index + 1 }}. {{ $question['text'] }}</p>
                                <p>����: {{ $question['answer'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- ������x���킷��{�^�� --}}
                    <div class="text-center">
                        <a href="{{ route('quiz.start') }}"
                           class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                            ������x���킷��
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
