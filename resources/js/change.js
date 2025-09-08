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

//難易度選択をする画面に行くとき用
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
                selectYaku(selectdifficultyId) {
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
                                @click="selectYaku(difficulty.id)"
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

//役選択画面に行くとき用
function selectyaku(selectdifficulty) {
    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.selectyakuUrl;
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
            window.tempYakuData = {
                selectedTag: data.selectedTag,
                selectedCourseId: data.selectedCourseId,
                selectdifficultyId: data.selectedDifficultyId,
                courseName: data.courseName,
                difficultyName: data.difficultyName,
                Yakusdata: data.Yakusdata,
                selectDifficulty(selectcourseId) {
                    selectdifficulty(selectcourseId);
                },
                selectHai(yakuId) {
                    selecthai(yakuId);
                },
            };

            let html = "";
            html += `<div x-data="window.tempYakuData" class="space-y-3">
                        <h3 class="text-lg font-bold mb-4">獲得したい役を選択してください</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" 
                           x-text="(courseName || 'コース') + ' > ' + (difficultyName || '難易度')"></p>
                        <button @click="selectDifficulty(selectedCourseId)" class="mt-4 text-sm text-gray-500 hover:underline">
                            &laquo; 戻る
                        </button>
                        
                        <template x-for="(yaku, index) in Yakusdata" :key="yaku.id">
                            <button 
                                @click="selectHai(yaku.id)"
                                class="block w-full text-left p-4 rounded-lg transition mb-3 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-800">
                                <div class="font-semibold" x-text="yaku.name"></div>
                            </button>
                        </template>

                        <div x-show="Yakusdata.length === 0" class="text-center py-8 text-gray-500">
                            <p>この難易度に学習可能な役がありません</p>
                        </div>
                    </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

//牌選択画面に行くとき用
//牌選択画面に行くとき用
//牌選択画面に行くとき用
function selecthai(selectyakuId) {
    console.log("selecthai called with yakuId:", selectyakuId);

    const changeElement = document.getElementById("change");
    const url = changeElement.dataset.selecthaiUrl;
    const csrfToken = changeElement.dataset.csrfToken;

    console.log("Request URL:", url);
    console.log("CSRF Token:", csrfToken);

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ selectyakuId: selectyakuId }),
    })
        .then((res) => {
            console.log("Response status:", res.status);
            console.log("Response headers:", res.headers);

            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }

            // レスポンステキストを確認
            return res.text().then((text) => {
                console.log("Raw response:", text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("JSON parse error:", e);
                    console.error("Response text:", text);
                    throw new Error("Invalid JSON response");
                }
            });
        })
        .then((data) => {
            console.log("Parsed data:", data);

            if (data.error) {
                console.error("Server error:", data.message);
                document.getElementById("change").innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <p>エラーが発生しました: ${data.message}</p>
                        <button onclick="selectyaku(${
                            data.selectedDifficultyId || "null"
                        })" 
                                class="mt-4 text-sm text-gray-500 hover:underline">
                            &laquo; 戻る
                        </button>
                    </div>
                `;
                return;
            }

            window.tempHaiData = {
                selectedTag: data.selectedTag,
                selectedCourseId: data.selectedCourseId,
                selectdifficultyId: data.selectedDifficultyId,
                selectedYakuId: data.selectedYakuId,
                courseName: data.courseName || "コース",
                difficultyName: data.difficultyName || "難易度",
                yakuName: data.yakuName || "役",
                Haisdata: data.Haisdata || [],
                selectYaku(selectdifficultyId) {
                    selectyaku(selectdifficultyId);
                },
                startGame(haiId, courseId, difficultyId) {
                    // クイズページへリダイレクト
                    const yakuId = this.selectedYakuId;
                    window.location.href = `/quiz/start?yaku_id=${yakuId}&tile_id=${haiId}&course_id=${courseId}&difficulty_id=${difficultyId}`;
                },
            };

            let html = "";
            html += `<div x-data="window.tempHaiData" class="space-y-3">
                        <h3 class="text-lg font-bold mb-4">獲得する牌を選択してください</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" 
                           x-text="courseName + ' > ' + difficultyName + ' > ' + yakuName"></p>
                        <button @click="selectYaku(selectdifficultyId)" 
                                class="mt-4 text-sm text-gray-500 hover:underline">
                            &laquo; 戻る
                        </button>
                        
                        <!-- 牌を表示（グリッド形式、1行7枚） -->
                        <div x-show="Haisdata && Array.isArray(Haisdata) && Haisdata.length > 0" class="space-y-2">
                            <!-- 上段 -->
                            <div class="flex justify-center space-x-1">
                                <template x-for="(tile, index) in Haisdata.slice(0, 7)" :key="tile.id + '_' + index + '_top'">
                                    <button @click="startGame(tile.id)"
                                            class="flex-1 aspect-[2/3] bg-white border border-gray-300 rounded hover:border-orange-500 transition-colors overflow-hidden shadow-sm hover:shadow-md cursor-pointer">
                                        <img x-show="tile.image_path" 
                                            :src="'/assets/tiles/' + tile.image_path" 
                                            :alt="tile.name || 'tile'" 
                                            class="w-full h-full object-contain"
                                            loading="lazy"
                                            @error="$event.target.src='/assets/tiles/default.png'">
                                    </button>
                                </template>
                            </div>

                            <!-- 下段 -->
                            <div x-show="Haisdata.length > 7" class="flex justify-center space-x-1 mt-2">
                                <template x-for="(tile, index) in Haisdata.slice(7, 14)" :key="tile.id + '_' + index + '_bottom'">
                                    <button @click="startGame(tile.id)"
                                            class="flex-1 aspect-[2/3] bg-white border border-gray-300 rounded hover:border-orange-500 transition-colors overflow-hidden shadow-sm hover:shadow-md cursor-pointer">
                                        <img x-show="tile.image_path" 
                                            :src="'/assets/tiles/' + tile.image_path" 
                                            :alt="tile.name || 'tile'" 
                                            class="w-full h-full object-contain"
                                            loading="lazy"
                                            @error="$event.target.src='/assets/tiles/default.png'">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- データがない場合のメッセージ -->
                        <div x-show="!Haisdata || !Array.isArray(Haisdata) || Haisdata.length === 0" 
                             class="text-center py-8 text-gray-500">
                            <p>この役に学習可能な牌がありません</p>
                            <p class="text-xs mt-2">デバッグ: データ = <span x-text="JSON.stringify(Haisdata)"></span></p>
                        </div>
                    </div>`;
            document.getElementById("change").innerHTML = html;
        })
        .catch((error) => {
            console.error("Error in selecthai:", error);
            document.getElementById("change").innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p>エラーが発生しました: ${error.message}</p>
                    <button onclick="history.back()" 
                            class="mt-4 text-sm text-gray-500 hover:underline">
                        &laquo; 戻る
                    </button>
                </div>
            `;
        });
} // windowオブジェクトに関数を明示的に追加
window.backfirst = backfirst;
window.selectcourse = selectcourse;
window.selectdifficulty = selectdifficulty;
window.selectyaku = selectyaku;
window.selecthai = selecthai;
