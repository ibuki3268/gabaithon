//コース選択から新規既存選択画面に戻るボタン用
function backfirst() {
    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.dashboardUrl;

    fetch(url, {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then((data) => {
            let html = "";
            html += `<div x-data="{ 
                        hasProgress: ${data.hasProgress},
                        selectCourse(type) {
                            selectcourse(type);
                        }
                    }">
                        <h3 class="text-lg font-bold mb-4">学習モードを選択してください</h3>
                        <button @click="selectCourse('new')"
                            class="w-full text-left p-4 rounded-lg transition bg-orange-500 hover:bg-orange-600 text-white">
                            <div class="font-semibold">新規学習</div>
                            <div class="text-sm opacity-75">新しいコースを開始する</div>
                        </button>
                        <button @click="selectCourse('existing')"
                                :class="hasProgress ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                class="w-full text-left p-4 rounded-lg transition"
                                :disabled="!hasProgress">
                            <div class="font-semibold">既存学習</div>
                            <div class="text-sm opacity-75">学習途中のコースを続ける</div>
                        </button>
                    </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

//新規か既存か選んでコース選択画面に行くとき用
function selectcourse(selectedtag) {
    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.selectcourseUrl;
    const csrfToken = changeElement.dataset.csrfToken;

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ selectedtag: selectedtag }),
    })
        .then((res) => res.json())
        .then((data) => {
            window.tempCourseData = {
                selectedtag: data.selectedtag,
                Courses: data.Courses,
                backToFirst() {
                    backfirst();
                },
                selectDifficulty(selectedcourseId) {
                    selectdifficulty(selectedcourseId);
                },
            };

            let html = "";
            html += `<div x-data="window.tempCourseData" class="space-y-3">
                    <h3 class="text-lg font-bold mb-4" x-text="selectedtag === 'existing' ? '続きから学習するコースを選択してください' : '新しく学習するコースを選択してください'"></h3>
                    <button @click="backToFirst()" class="mt-4 text-sm text-gray-500 hover:underline">
                        &laquo; 戻る
                    </button>
                    <div class="space-y-3">
                        <template x-for="(course, index) in Courses" :key="course.id">
                            <button @click="selectDifficulty(course.id)"
                                    class="w-full text-left p-4 rounded-lg transition bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800">
                                <div class="font-semibold" x-text="course.name"></div>
                                <p x-text="course.created_by ? course.created_by : '運営'"></p>
                                <div class="text-sm opacity-75" x-text="course.description"></div>
                            </button>
                        </template>
                    </div>
                </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

//ディフィカルティ選択の関数
function selectdifficulty(selectedcourse) {
    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.selectdifficultyUrl;
    const csrfToken = changeElement.dataset.csrfToken;

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ selectedCourseId: selectedcourse }),
    })
        .then((res) => res.json())
        .then((data) => {
            window.tempCourseData = {
                selectedTag: data.selectedTag,
                selectedCourseId: data.selectedCourseId,
                Difficulties: data.Difficulties,
                courseName: data.courseName,
                selectCourse(selectcourseId) {
                    selectcourse(selectcourseId);
                },
                selectyaku(selectdifficultyId) {
                    selectyaku(selectdifficultyId);
                },
            };

            let html = "";
            html += `<div x-data="window.tempCourseData" class="space-y-3">
                        <h3 class="text-lg font-bold mb-4">学習難易度を選択してください</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" x-text="courseName + ' (' + (selectedTag === 'new' ? '新規学習' : '既存学習') + ')'"></p>
                        <button @click="selectCourse(selectedTag)" class="mt-4 text-sm text-gray-500 hover:underline">
                            &laquo; 戻る
                        </button>
                        
                        <template x-for="(difficulty, index) in Difficulties" :key="difficulty.id">
                            <button 
                                @click="selectyaku(difficulty.id)"
                                class="w-full text-left p-4 rounded-lg transition bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800">
                                <div class="font-semibold" x-text="difficulty.name"></div>
                                <p x-text="'難易度: ' + difficulty.level"></p>
                                <div class="text-sm opacity-75" x-text="difficulty.description"></div>
                            </button>
                        </template>

                        <div x-show="Difficulties.length === 0" class="text-center py-8 text-gray-500">
                            <p x-text="selectedTag === 'new' ? '新しく学習できる難易度がありません' : '学習中の難易度がありません'"></p>
                        </div>
                    </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

function selectyaku(selectdifficulty) {
    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.selectdifficultyUrl;
    const csrfToken = changeElement.dataset.csrfToken;

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ selectdifficultyId: selectdifficulty }),
    })
        .then((res) => res.json())
        .then((data) => {
            window.tempCourseData = {
                selectedTag: data.selectedTag,
                selectedCourseId: data.selectedCourseId,
                selectdifficultyId: data.selectdifficultyId,
                courseName: data.courseName,
                difficultyName: data.difficultyName,
                Yakus: data.yakus,
                selectdifficulty(courseId) {
                    selectdifficulty(courseId);
                },
                selecthai(haiId) {
                    selecthai(haiId);
                },
            };

            let html = "";
            html += `<div x-data="window.tempCourseData" class="space-y-3">
                        <h3 class="text-lg font-bold mb-4">学習難易度を選択してください</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" x-text="courseName + ' (' + (selectedTag === 'new' ? '新規学習' : '既存学習') + ')'"></p>
                        <button @click="selectSelectCourse(selectedCourseId)" class="mt-4 text-sm text-gray-500 hover:underline">
                            &laquo; 戻る
                        </button>
                        
                        <template x-for="(difficulty, index) in Difficulties" :key="difficulty.id">
                            <button 
                                @click=selectyaku(difficulty.id)
                                class="w-full text-left p-4 rounded-lg transition bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800">
                                <div class="font-semibold" x-text="difficulty.name"></div>
                                <p x-text="'難易度: ' + difficulty.level"></p>
                                <div class="text-sm opacity-75" x-text="difficulty.description"></div>
                            </button>
                        </template>

                        <div x-show="Difficulties.length === 0" class="text-center py-8 text-gray-500">
                            <p x-text="selectedTag === 'new' ? '新しく学習できる難易度がありません' : '学習中の難易度がありません'"></p>
                        </div>
                    </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

// windowオブジェクトに関数を明示的に追加
window.backfirst = backfirst;
window.selectcourse = selectcourse;
window.selectdifficulty = selectdifficulty;
window.selectyaku = selectyaku;
