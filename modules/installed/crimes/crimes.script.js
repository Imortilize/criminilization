const radioButtons = document.getElementsByName("crime-select");
if (radioButtons.length) {
	// We have radio buttons to select, so select the last one the user select
	// by default, or the first one if they havent selected one recently
	for (let i = 0; i < radioButtons.length; i++) {
		const radioButton = radioButtons[i];
		radioButton.checked = (i == 0);
	}

	const btn = document.querySelector('#crime-btn'); 
	if (btn) {
		btn.onclick = function (event) {
			event.preventDefault();
			let index = -1;
			for (let j = 0; j < radioButtons.length; j++) {
				const radioButton = radioButtons[j];
				if (radioButton.checked) {
					const radioButtonIdString = radioButton.id;
					const radioButtonId = radioButtonIdString.replace("crime", "");
					index = parseInt(radioButtonId);
					break;
				}
			}
		
			if (index < 0) {
				// No button found selected (should never happen)
				return;
			}
			const crimeIndex = ("crime=" + index.toString());
			const href = (btn.href + "&" + crimeIndex);
			location.href = href;
		};
	}
}