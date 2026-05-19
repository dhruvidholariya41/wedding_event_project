<form method="POST" action="book_event.php">

    Event Name
    <select name="event_name" required>

        <option value="Mehndi">Mehndi</option>
        <option value="Haldi">Haldi</option>
        <option value="Mandap Muhurat">Mandap Muhurat</option>
        <option value="Sangeet">Sangeet</option>
        <option value="Wedding">Wedding</option>
        <option value="Reception">Reception</option>

    </select>

    Event Date
    <input type="date" name="event_date" required>

    Venue
    <input type="text" name="venue" required>

    <button type="submit" name="book_event">Book Event</button>

</form>