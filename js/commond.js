const legacyForm = document.getElementById("formVenta");

if (legacyForm) {
	legacyForm.addEventListener("submit", function (event) {
		event.preventDefault();

		const payload = new FormData(this);

		fetch("../public/index.php?page=api_insert", {
			method: "POST",
			body: payload,
		})
			.then((response) => response.json())
			.then((result) => {
				alert(result.message || "Operacion procesada");
				if (result.ok) {
					legacyForm.reset();
				}
			})
			.catch(() => {
				alert("No se pudo completar la solicitud");
			});
	});
}