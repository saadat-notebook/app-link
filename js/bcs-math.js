document.addEventListener('DOMContentLoaded', () => {

    // --- DOM Elements ---
    const setupView = document.getElementById('quiz-setup-view');
    const quizView = document.getElementById('quiz-active-view');
    const resultsView = document.getElementById('quiz-results-view');

    const numQuestionsInput = document.getElementById('num-questions');
    const timeLimitInput = document.getElementById('time-limit');
    const startQuizBtn = document.getElementById('start-quiz-btn');

    const progressText = document.getElementById('quiz-progress');
    const timerText = document.getElementById('quiz-timer');
    const questionText = document.getElementById('question-text');
    const optionsContainer = document.getElementById('options-container');
    const nextQuestionBtn = document.getElementById('next-question-btn');
    const timerProgressBar = document.getElementById('timer-progress-bar'); // NEW

    const finalScoreText = document.getElementById('final-score');
    const correctAnswersText = document.getElementById('correct-answers');
    const incorrectAnswersText = document.getElementById('incorrect-answers');
    const unansweredText = document.getElementById('unanswered-questions');
    const timeTakenText = document.getElementById('time-taken');

    const reviewAnswersBtn = document.getElementById('review-answers-btn');
    const practiceAgainBtn = document.getElementById('practice-again-btn');
    const reviewSection = document.getElementById('review-section');
    const reviewAccordionContainer = document.getElementById('review-accordion-container');

    // NEW: Custom Modal Elements
    const resumeModal = document.getElementById('resume-modal');
    const resumeYesBtn = document.getElementById('resume-yes-btn');
    const resumeNoBtn = document.getElementById('resume-no-btn');


    // --- State Variables ---
    let allQuestions = [];
    let currentQuizQuestions = [];
    let userAnswers = [];
    let currentQuestionIndex = 0;
    let score = 0;
    let timerInterval;
    let timeRemaining = 0;
    let totalTime = 0;
    const answeredQuestionIds = JSON.parse(localStorage.getItem('bcsAnsweredIds')) || [];

    // --- Functions ---

    async function loadQuestions() {
        try {
            const response = await fetch('bcs-questions.json');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            allQuestions = data;
        } catch (error) {
            console.error('Error fetching questions:', error);
            questionText.textContent = "Failed to load questions. Please check the console and refresh the page.";
        }
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    // ===== Functions for Session Storage & Custom Modal =====

    function saveQuizState() {
        if (!quizView.classList.contains('active')) return;
        const state = { currentQuizQuestions, userAnswers, currentQuestionIndex, timeRemaining, totalTime };
        sessionStorage.setItem('bcsQuizState', JSON.stringify(state));
    }

    function resumeQuiz(state) {
        currentQuizQuestions = state.currentQuizQuestions;
        userAnswers = state.userAnswers;
        currentQuestionIndex = state.currentQuestionIndex;
        timeRemaining = state.timeRemaining;
        totalTime = state.totalTime;
        displayQuestion();
        startTimer();
        switchView(quizView);
        if (userAnswers[currentQuestionIndex]) {
            document.querySelectorAll('.quiz-option-btn').forEach(btn => {
                btn.disabled = true;
                if (btn.textContent === userAnswers[currentQuestionIndex]) {
                    btn.classList.add('selected');
                }
            });
        }
    }

    // NEW: Function to handle the custom resume prompt
    function promptToResume() {
        return new Promise(resolve => {
            resumeModal.classList.add('is-visible');

            resumeYesBtn.addEventListener('click', () => {
                resumeModal.classList.remove('is-visible');
                resolve(true); // User wants to resume
            }, { once: true }); // Listener runs only once

            resumeNoBtn.addEventListener('click', () => {
                resumeModal.classList.remove('is-visible');
                resolve(false); // User wants to start a new quiz
            }, { once: true });
        });
    }

    function switchView(viewToShow) {
        [setupView, quizView, resultsView].forEach(view => view.classList.remove('active'));
        viewToShow.classList.add('active');
    }

    function startQuiz() {
        const numQuestions = parseInt(numQuestionsInput.value, 10);
        totalTime = parseInt(timeLimitInput.value, 10) * 60;
        timeRemaining = totalTime;

        let availableQuestions = allQuestions.filter(q => !answeredQuestionIds.includes(q.question_number || q.id));
        if (availableQuestions.length < numQuestions) {
            console.log("Not enough new questions, reusing all questions.");
            availableQuestions = [...allQuestions];
        }

        shuffleArray(availableQuestions);
        currentQuizQuestions = availableQuestions.slice(0, numQuestions);
        userAnswers = new Array(currentQuizQuestions.length).fill(null);
        currentQuestionIndex = 0;
        score = 0;
        reviewSection.classList.add('hidden');
        reviewAccordionContainer.innerHTML = '';

        if (currentQuizQuestions.length > 0) {
            timerProgressBar.style.width = '100%'; // NEW: Reset progress bar
            displayQuestion();
            startTimer();
            switchView(quizView);
            saveQuizState();
        } else {
            alert("No questions available to start the quiz.");
        }
    }

    function displayQuestion() {
        const question = currentQuizQuestions[currentQuestionIndex];
        progressText.textContent = `Question: ${currentQuestionIndex + 1} / ${currentQuizQuestions.length}`;
        questionText.textContent = question.question;
        optionsContainer.innerHTML = '';
        const shuffledOptions = [...question.options];
        shuffleArray(shuffledOptions);
        shuffledOptions.forEach(option => {
            const button = document.createElement('button');
            button.className = 'quiz-option-btn';
            button.textContent = option;
            button.addEventListener('click', () => selectAnswer(option, button));
            optionsContainer.appendChild(button);
        });
        nextQuestionBtn.textContent = (currentQuestionIndex === currentQuizQuestions.length - 1) ? 'Finish Quiz' : 'Next Question';
    }

    function selectAnswer(selectedOption, selectedButton) {
        userAnswers[currentQuestionIndex] = selectedOption;
        document.querySelectorAll('.quiz-option-btn').forEach(btn => {
            btn.disabled = true;
        });
        selectedButton.classList.add('selected');
        saveQuizState();
    }

    function goToNextQuestion() {
        const currentId = currentQuizQuestions[currentQuestionIndex].question_number || currentQuizQuestions[currentQuestionIndex].id;
        if (!answeredQuestionIds.includes(currentId)) {
            answeredQuestionIds.push(currentId);
        }
        if (currentQuestionIndex < currentQuizQuestions.length - 1) {
            currentQuestionIndex++;
            displayQuestion();
            saveQuizState();
        } else {
            sessionStorage.removeItem('bcsQuizState');
            finishQuiz();
        }
    }

    function startTimer() {
        clearInterval(timerInterval);
        timerInterval = setInterval(updateTimer, 1000);
    }

    function updateTimer() {
        if (timeRemaining > 0) {
            timeRemaining--;
            const minutes = Math.floor(timeRemaining / 60).toString().padStart(2, '0');
            const seconds = (timeRemaining % 60).toString().padStart(2, '0');
            timerText.textContent = `Time: ${minutes}:${seconds}`;

            // NEW: Update progress bar width
            timerProgressBar.style.width = `${(timeRemaining / totalTime) * 100}%`;
        } else {
            finishQuiz();
        }
    }

    function finishQuiz() {
        clearInterval(timerInterval);
        sessionStorage.removeItem('bcsQuizState');
        localStorage.setItem('bcsAnsweredIds', JSON.stringify(answeredQuestionIds));
        score = 0;
        let correctCount = 0;
        let incorrectCount = 0;
        currentQuizQuestions.forEach((q, index) => {
            if (userAnswers[index] === q.answer) {
                score++;
                correctCount++;
            } else if (userAnswers[index] !== null) {
                incorrectCount++;
            }
        });
        const unansweredCount = currentQuizQuestions.length - (correctCount + incorrectCount);
        finalScoreText.textContent = `${score} / ${currentQuizQuestions.length}`;
        correctAnswersText.textContent = correctCount;
        incorrectAnswersText.textContent = incorrectCount;
        unansweredText.textContent = unansweredCount;
        const timeSpent = totalTime - timeRemaining;
        const minutes = Math.floor(timeSpent / 60);
        const seconds = (timeSpent % 60).toString().padStart(2, '0');
        timeTakenText.textContent = `${minutes}:${seconds}`;
        switchView(resultsView);
    }

    // MODIFIED: This function now ONLY generates the review content
    function generateReviewContent() {
        reviewAccordionContainer.innerHTML = '';
        currentQuizQuestions.forEach((question, index) => {
            const userAnswer = userAnswers[index];
            const isCorrect = userAnswer === question.answer;
            const iconClass = isCorrect ? 'fa-check-circle correct' : 'fa-times-circle incorrect';
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
            let headerHTML = `<button class="accordion-header"><span>${index + 1}. ${question.question}</span><i class="fas ${iconClass}"></i></button>`;
            let contentHTML = `<div class="accordion-content"><div class="review-options">`;
            question.options.forEach(option => {
                let optionClass = '';
                if (option === question.answer) optionClass = 'correct';
                else if (option === userAnswer && !isCorrect) optionClass = 'incorrect';
                contentHTML += `<p class="${optionClass}">${option}</p>`;
            });
            contentHTML += `</div><div class="review-explanation"><h4>Explanation:</h4><p>${question.explanation}</p></div></div>`;
            accordionItem.innerHTML = headerHTML + contentHTML;
            reviewAccordionContainer.appendChild(accordionItem);
        });

        reviewAccordionContainer.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                const content = header.nextElementSibling;
                if (item.classList.contains('active')) {
                    item.classList.remove('active');
                    content.style.maxHeight = null;
                } else {
                    item.classList.add('active');
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });
        });
    }

    // --- Event Listeners ---
    startQuizBtn.addEventListener('click', startQuiz);
    nextQuestionBtn.addEventListener('click', goToNextQuestion);
    practiceAgainBtn.addEventListener('click', () => switchView(setupView));

    // MODIFIED: Event listener for the toggle review button
    reviewAnswersBtn.addEventListener('click', () => {
        const isHidden = reviewSection.classList.contains('hidden');
        if (isHidden) {
            if (reviewAccordionContainer.innerHTML === '') {
                generateReviewContent();
            }
            reviewSection.classList.remove('hidden');
            reviewAnswersBtn.textContent = 'Hide Review';
        } else {
            reviewSection.classList.add('hidden');
            reviewAnswersBtn.textContent = 'Review Answers';
        }
    });

    // MODIFIED: Page Initialization Logic with Custom Modal
    async function initializeQuizPage() {
        await loadQuestions();
        const savedStateJSON = sessionStorage.getItem('bcsQuizState');
        if (savedStateJSON) {
            const userWantsToResume = await promptToResume();
            if (userWantsToResume) {
                const savedState = JSON.parse(savedStateJSON);
                resumeQuiz(savedState);
            } else {
                sessionStorage.removeItem('bcsQuizState');
            }
        }
    }

    initializeQuizPage();
});