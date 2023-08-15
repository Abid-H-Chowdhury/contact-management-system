// ajax script for getting thana data

$(document).on("change", "#district", function () {
    var districtID = $(this).val();
    if (districtID) {
        $.ajax({
            type: "POST",
            url: "backend-script.php",
            data: { id: districtID },
            success: function (result) {
                $("#thana").html(result);
            },
        });
    } else {
        $("#thana").html('<option value="">Select thana</option>');
    }
});
