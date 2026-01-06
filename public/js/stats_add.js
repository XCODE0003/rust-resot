// Show stats

$(function () {
  $('.select-stats.active .select-dropdown.toggle div').on('click', function() {
    location.href = "{{ route('logs.statistics_game_items', ['type' => $data["type"], 'server_id' => $data["server_id"]]) }}&item_id=" + $('#item_id').val();
  });
});