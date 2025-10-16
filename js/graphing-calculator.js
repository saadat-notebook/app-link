document.addEventListener('DOMContentLoaded', () => {
    const graphContainer = document.getElementById('graph-container');
    if (!graphContainer) return;

    // 1. Initialize the Desmos Graphing Calculator
    const calculator = Desmos.GraphingCalculator(graphContainer, {
        keypad: true,
        expressions: true,
        settingsMenu: true,
        zoomButtons: true
    });

    // 2. Set ONLY the single "y=" placeholder for the user
    calculator.setExpression({ id: 'placeholder', latex: 'y=' });

    // 3. Function to handle theme changes
    function setDesmosTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark-mode');
        calculator.updateSettings({
            invertedColors: isDarkMode
        });
    }

    // 4. Set the initial theme when the calculator loads
    setDesmosTheme();

    // 5. Automatically update the theme if the user changes it
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                setDesmosTheme();
            }
        }
    });

    observer.observe(document.documentElement, { attributes: true });
});