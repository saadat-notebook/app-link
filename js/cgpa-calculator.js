document.addEventListener('DOMContentLoaded', () => {
    // --- Course Data ---
    const allCourseData = {
        '1': { core: [{ name: "Fundamentals of Mathematics", credits: 3 }, { name: "Calculus – I", credits: 3 }, { name: "Linear Algebra", credits: 3 }, { name: "Analytic and Vector Geometry", credits: 3 }], optionGroups: [{ groupName: "Chemistry", courses: [{ name: "Chemistry-I", credits: 4 }, { name: "Chemistry-I Practical", credits: 2 }] }, { groupName: "Statistics", courses: [{ name: "Introduction to Statistics", credits: 4 }, { name: "Statistics Practical-I", credits: 2 }] }, { groupName: "Physics", courses: [{ name: "Physics-I (Mechanics, Waves, Optics)", credits: 4 }, { name: "Physics-II (Heat, Thermodynamics)", credits: 2 }] }], compulsory: [{ name: "History of the Emergence of Independent Bangladesh", credits: 4 }], info: "Select exactly TWO of the following optional groups." },
        '2': { core: [{ name: "Calculus –II", credits: 4 }, { name: "Ordinary Differential Equations", credits: 4 }, { name: "Computer Programming (Fortran)", credits: 4 }, { name: "Math Lab (Practical)", credits: 4 }], optionGroups: [{ groupName: "Physics", courses: [{ name: "Physics-III (Electricity and Modern Physics)", credits: 4 }, { name: "Physics-IV (Physics Practical)", credits: 2 }] }, { groupName: "Chemistry", courses: [{ name: "General Chemistry-II", credits: 4 }, { name: "Environmental Chemistry", credits: 2 }] }, { groupName: "Statistics", courses: [{ name: "Methods of Statistics", credits: 4 }, { name: "Statistics Practical-II", credits: 2 }] }], compulsory: [], info: "Select exactly TWO of the following optional groups. English is non-credit and not included in SGPA." }
    };

    // --- DOM Element Getters ---
    const calculatorContainer = document.getElementById('calculator-container');
    const resultModal = document.getElementById('sgpa-result-modal');
    const modalSgpaEl = document.getElementById('modal-sgpa-value');
    const modalCreditsEl = document.getElementById('modal-credits-value');
    const modalPointsEl = document.getElementById('modal-points-value');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const modalClassDisplay = document.getElementById('modal-class-display');
    const modalDownloadBtn = document.getElementById('modal-download-image-btn');
    const validationModal = document.getElementById('validation-error-modal');
    const modalValidationOkBtn = document.getElementById('modal-validation-ok-btn');
    
    let currentCoursesForPDF = [];

    // --- UI & Core Logic Functions ---
    const buildCalculatorUI = (yearData) => { let ui_html = `<div class="calculator-card"><h3>Core Courses</h3><div class="courses-group">${yearData.core.map(course => createCourseRowHTML(course)).join('')}</div><div class="optional-group-selection"><h3>${yearData.info}</h3><div class="group-checkboxes">${yearData.optionGroups.map((group, index) => `<label><input type="checkbox" name="optionalGroup" value="${index}"> ${group.groupName}</label>`).join('')}</div></div><div id="selected-optional-courses-container"></div>${yearData.compulsory.length > 0 ? `<h3 style="margin-top: 1.5rem;">Compulsory Courses</h3><div class="courses-group">${yearData.compulsory.map(course => createCourseRowHTML(course)).join('')}</div>` : ''}<div class="calculator-actions" style="margin-top: 1.5rem;"><button id="calculate-btn" class="btn-primary">Calculate SGPA</button></div></div>`; calculatorContainer.innerHTML = ui_html; attachEventListeners(yearData); }
    const createCourseRowHTML = (course) => { return `<div class="course-row" data-credits="${course.credits}"><div class="course-name">${course.name}</div><div class="course-credits-display">${course.credits} Credits</div><div class="course-grade-container"><select class="course-grade"><option value="" disabled selected>Select Grade</option><option value="4.00">A+</option><option value="3.75">A</option><option value="3.50">A-</option><option value="3.25">B+</option><option value="3.00">B</option><option value="2.75">B-</option><option value="2.50">C+</option><option value="2.25">C</option><option value="2.00">D</option><option value="0.00">F</option></select></div></div>`; }
    const attachEventListeners = (yearData) => { document.getElementById('calculate-btn').addEventListener('click', calculateSGPA); document.querySelectorAll('input[name="optionalGroup"]').forEach(checkbox => { checkbox.addEventListener('change', () => { if (document.querySelectorAll('input[name="optionalGroup"]:checked').length > 2) { alert('Please select only TWO optional groups.'); checkbox.checked = false; return; } updateOptionalCoursesDisplay(yearData); }); }); }
    const updateOptionalCoursesDisplay = (yearData) => { const container = document.getElementById('selected-optional-courses-container'); container.innerHTML = ''; document.querySelectorAll('input[name="optionalGroup"]:checked').forEach(checkbox => { const group = yearData.optionGroups[checkbox.value]; group.courses.forEach(course => { container.innerHTML += createCourseRowHTML(course); }); }); }
    const getDivisionFromSGPA = (sgpa) => { if (sgpa >= 3.00) return { text: "First Class", className: "first-class" }; if (sgpa >= 2.25) return { text: "Second Class", className: "second-class" }; if (sgpa >= 2.00) return { text: "Third Class", className: "third-class" }; return { text: "Fail", className: "fail-class" }; }
    
    const calculateSGPA = () => {
        let totalCredits = 0, totalGradePoints = 0, allGradesSelected = true;
        const activeCourses = document.querySelectorAll('#calculator-container .course-row');
        currentCoursesForPDF = [];
        if (activeCourses.length === 0) {
            alert("Please select your optional courses to calculate SGPA.");
            return;
        }
        activeCourses.forEach(row => { if (row.querySelector('.course-grade').value === "") allGradesSelected = false; });
        if (!allGradesSelected) {
            validationModal.classList.add('is-visible');
            return;
        }
        activeCourses.forEach(row => {
            const credits = parseFloat(row.dataset.credits);
            const gradePoint = parseFloat(row.querySelector('.course-grade').value);
            totalCredits += credits;
            totalGradePoints += credits * gradePoint;
            const selectEl = row.querySelector('.course-grade');
            currentCoursesForPDF.push({
                name: row.querySelector('.course-name').textContent,
                credits: row.dataset.credits,
                gradeText: selectEl.options[selectEl.selectedIndex].text,
                gradePoint: parseFloat(selectEl.value).toFixed(2)
            });
        });
        const sgpa = (totalCredits > 0) ? (totalGradePoints / totalCredits) : 0;
        const division = getDivisionFromSGPA(sgpa);
        const classSpan = modalClassDisplay.querySelector('span');
        classSpan.textContent = division.text;
        classSpan.className = ''; classSpan.classList.add(division.className);
        modalSgpaEl.textContent = sgpa.toFixed(2);
        modalCreditsEl.textContent = totalCredits;
        modalPointsEl.textContent = totalGradePoints.toFixed(2);
        resultModal.classList.add('is-visible');
    }

    // --- Final, Robust Image Generation Function with Native Wrapper Support ---
    function downloadResultAsImage() {
        modalDownloadBtn.classList.add('is-loading');
        modalDownloadBtn.disabled = true;

        const userName = localStorage.getItem('saadatNotesUserName') || 'Student';
        const userYear = localStorage.getItem('saadatNotesUserYear') || 'N/A';
        const generationDate = new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
        const sgpa = modalSgpaEl.textContent;
        const totalCredits = modalCreditsEl.textContent;
        const division = modalClassDisplay.querySelector('span').textContent;

        let courseRowsHTML = '';
        currentCoursesForPDF.forEach(course => {
            courseRowsHTML += `<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: left;">${course.name}</td><td style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: center;">${course.credits}</td><td style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: center;">${course.gradeText}</td><td style="padding: 12px 10px; text-align: center;">${course.gradePoint}</td></tr>`;
        });
        
        const imageContentHTML = `<div style="width: 800px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #333; background-color: #fff; box-sizing: border-box;"><div style="padding: 30px;"><table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;"><tbody><tr><td style="background-color: #4A4A4A; color: white; padding: 15px 25px; width: 70%;"><h1 style="margin: 0; font-size: 28px; font-weight: bold; text-align: left;">Saadat Notebook</h1></td><td style="background-color: #616161; color: white; padding: 15px 25px; border-left: 1px solid #757575;"><p style="margin: 0; font-size: 14px; opacity: 0.9; text-align: right;">SGPA Result Sheet</p></td></tr></tbody></table><div style="margin-bottom: 25px; font-size: 14px;"><p style="margin: 5px 0;">Name: <b style="font-weight: bold;">${userName}</b></p><p style="margin: 5px 0;">Academic Year: <b style="font-weight: bold;">${userYear}</b></p></div><div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-left: 5px solid #28a745; padding: 20px; text-align: center; margin-bottom: 30px; border-radius: 5px;"><h2 style="margin: 0; font-size: 32px; color: #28a745;">SGPA: ${sgpa}</h2><p style="margin: 8px 0 0; font-size: 16px; color: #6c757d;">Division: ${division} &nbsp;&nbsp;|&nbsp;&nbsp; Total Credits: ${totalCredits}</p></div><h3 style="font-size: 18px; color: #343a40; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 10px; font-weight: bold;">Result Details</h3><table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; font-size: 14px;"><thead><tr style="background-color: #f2f2f2; font-weight: bold; color: #495057; border-bottom: 1px solid #ddd;"><th style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: left; width: 50%;">Course Name</th><th style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: center; width: 15%;">Credits</th><th style="padding: 12px 10px; border-right: 1px solid #ddd; text-align: center; width: 15%;">Grade</th><th style="padding: 12px 10px; text-align: center; width: 20%;">Grade Point</th></tr></thead><tbody>${courseRowsHTML}</tbody></table><div style="text-align: center; margin-top: 40px; font-size: 12px; color: #adb5bd;">Generated on ${generationDate} via Saadat Notebook</div></div></div>`;

        const renderContainer = document.createElement('div');
        renderContainer.style.position = 'fixed';
        renderContainer.style.top = '0';
        renderContainer.style.left = '0';
        renderContainer.style.zIndex = '-1';
        renderContainer.style.opacity = '0';
        renderContainer.innerHTML = imageContentHTML;
        document.body.appendChild(renderContainer);

        const elementToRender = renderContainer.firstElementChild;
        
        html2canvas(elementToRender, { scale: 2, useCORS: true })
            .then(canvas => {
                const fileName = `SGPA_Result_${userName.replace(/ /g, '_')}.png`;
                const dataUrl = canvas.toDataURL("image/png");

                // --- Critical Logic: Check for Android wrapper ---
                if (window.Android && typeof window.Android.downloadBase64Image === 'function') {
                    // App environment: pass Base64 data to native Android code
                    const base64Data = dataUrl.replace(/^data:image\/png;base64,/, "");
                    window.Android.downloadBase64Image(base64Data, fileName);
                } else {
                    // Browser environment: use the standard link-click method
                    const link = document.createElement('a');
                    link.download = fileName;
                    link.href = dataUrl;
                    link.click();
                }
            })
            .catch(err => {
                console.error("Image generation failed:", err);
                alert("Sorry, there was an error creating the image.");
            })
            .finally(() => {
                document.body.removeChild(renderContainer);
                modalDownloadBtn.classList.remove('is-loading');
                modalDownloadBtn.disabled = false;
            });
    }
    
    // --- Modal Control & Initialization ---
    const closeModal = (modalElement) => { modalElement.classList.remove('is-visible'); }
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', () => closeModal(resultModal));
    if (modalDownloadBtn) modalDownloadBtn.addEventListener('click', downloadResultAsImage);
    if (resultModal) resultModal.addEventListener('click', (e) => { if (e.target === resultModal) closeModal(resultModal); });
    if (modalValidationOkBtn) modalValidationOkBtn.addEventListener('click', () => closeModal(validationModal));
    if (validationModal) validationModal.addEventListener('click', (e) => { if (e.target === validationModal) closeModal(validationModal); });
    
    const initialize = () => {
        const userYearString = localStorage.getItem('saadatNotesUserYear');
        let yearKey = '1';
        if (userYearString === 'Honours 2nd Year') yearKey = '2';
        const data = allCourseData[yearKey];
        if (data && data.core.length > 0) { buildCalculatorUI(data); } 
        else { calculatorContainer.innerHTML = `<div class="info-card"><p>Course data for your academic year is not yet available.</p></div>`; }
    }
    
    initialize();
});