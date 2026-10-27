
document.addEventListener('DOMContentLoaded', function () {
    const onTopSelect = document.getElementById('on_top_id');
    const pointsWrapper = document.getElementById('points-wrapper');
    const pointsInput = document.getElementById('points');

    function togglePoints() {
        const selectedOption =
            onTopSelect.options[onTopSelect.selectedIndex];

        const isPointsCampaign =
            selectedOption?.dataset.subtype === 'POINTS';

        pointsWrapper.style.display = isPointsCampaign ? '' : 'none';
        pointsInput.disabled = !isPointsCampaign;

        if (!isPointsCampaign) {
            pointsInput.value = '0';
        }
    }

    onTopSelect.addEventListener('change', togglePoints);
    togglePoints();
});