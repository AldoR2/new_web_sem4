$("[data-validate]").on("input", function () {
    const field = $(this).attr("name");
    const value = $(this).val();
    const errorSelector = `#${field}_error`;
    const entity = $(this).data("validate");
    const editId = $("#edit_id").val();

    $.post({
        url: `/superadmin/validate-field/${entity}`,
        method: "POST",
        data: {
            field: field,
            value: value,
            id: editId,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function () {
            $(errorSelector).text("");
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                $(errorSelector).text(xhr.responseJSON.error);
            }
        },
    });
});
