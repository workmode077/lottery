@push('js')
    <script>
        $(document).ready(function() {
            $('#city_id').select2({
                placeholder: "Select City"
            });

            $('#state_id').on('change', function() {
                const stateId = $(this).val();
                $('#city_id').empty().trigger('change'); // Clear the city dropdown

                if (stateId) {
                    $('#city_id').select2({
                        placeholder: "Select City",
                        ajax: {
                            url: `/${prefix}/get-cities`,
                            data: params => ({
                                state_id: stateId,
                                search: params.term || '' // Ensure search is always defined
                            }),
                            processResults: data => ({
                                results: data
                            }),
                        }
                    });
                } else {
                    $('#city_id').val(null).trigger('change'); // Clear selection if no state is selected
                }
            });
        });
    </script>
@endpush
