<!-- <?php
session_start();
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$database = 'hhh'; 

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// session_start();
// if (!isset($_SESSION['username'])) {
//     $_SESSION['booking_pending'] = true;
//     header("Location: ../index.php");
//     exit();
// }

// Update room status to available if checkout date is less than current date
$currentDate = date('Y-m-d');
$updateQuery = "UPDATE booking SET bstatus = 'available' WHERE check_out < '$currentDate'";
$conn->query($updateQuery);

// Get booked rooms
$bookedQuery = "SELECT rno FROM booking WHERE bstatus != 'available'";
$bookedResult = $conn->query($bookedQuery);
$bookedRooms = array();
if ($bookedResult->num_rows > 0) {
    while($row = $bookedResult->fetch_assoc()) {
        $bookedRooms[] = $row['rno'];
    }
}

// Get available rooms
$availableQuery = "SELECT rno, rtype, rprice FROM room";
$availableResult = $conn->query($availableQuery);
$availableRooms = array();
if ($availableResult->num_rows > 0) {
    while($row = $availableResult->fetch_assoc()) {
        $availableRooms[$row['rno']] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a73e8;
            --secondary-color: #34495e;
            --available-color: #00c853;
            --booked-color: #d32f2f;
            --not-available-color: #757575;
            --background-color: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 1.8em;
            color: var(--primary-color);
        }

        .legend {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9em;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-available { background: var(--available-color); }
        .legend-booked { background: var(--booked-color); }
        .legend-not-available { background: var(--not-available-color); }

        .floor {
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .floor-header {
            padding: 12px 15px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .floor-title {
            font-size: 1.1em;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .floor-stats {
            font-size: 0.9em;
            display: flex;
            gap: 15px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .room-layout {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 8px;
            padding: 15px;
        }

        .room {
            aspect-ratio: 1;
            padding: 5px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.85em;
            transition: transform 0.2s ease;
            color: white;
        }

        .room i {
            font-size: 1em;
            margin-bottom: 3px;
        }

        .room.available {
            background: var(--available-color);
            cursor: pointer;
        }

        .room.available:hover {
            transform: scale(1.1);
        }

        .room.booked {
            background: var(--booked-color);
        }

        .room.not-available {
            background: var(--not-available-color);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 350px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            cursor: pointer;
            font-size: 1.2em;
            color: #666;
        }

        .modal-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.3em;
        }

        .room-detail {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 500;
            color: #333;
        }

        .book-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s ease;
        }

        .book-btn:hover {
            background: #1557b0;
        }

        @media (max-width: 768px) {
            .floor-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .floor-stats {
                flex-wrap: wrap;
                justify-content: center;
            }

            .room-layout {
                grid-template-columns: repeat(5, 1fr);
            }

            .legend {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Room Booking</h1>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color legend-available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-booked"></div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-not-available"></div>
                    <span>Not Available</span>
                </div>
            </div>
        </div>

        <?php
        for ($floor = 1; $floor <= 5; $floor++) {
            $startRoom = ($floor - 1) * 20 + 1;
            $endRoom = $floor * 20;
            
            $floorAvailable = 0;
            $floorBooked = 0;
            $floorNotAvailable = 0;

            // Pre-calculate floor statistics
            for ($i = $startRoom; $i <= $endRoom; $i++) {
                if (isset($availableRooms[$i])) {
                    if (in_array($i, $bookedRooms)) {
                        $floorBooked++;
                    } else {
                        $floorAvailable++;
                    }
                } else {
                    $floorNotAvailable++;
                }
            }

            echo "<div class='floor'>";
            echo "<div class='floor-header'>";
            echo "<div class='floor-title'><i class='fas fa-building'></i> Floor $floor</div>";
            echo "<div class='floor-stats'>";
            echo "<div class='stat-item'><i class='fas fa-check-circle'></i> $floorAvailable</div>";
            echo "<div class='stat-item'><i class='fas fa-times-circle'></i> $floorBooked</div>";
            echo "</div>";
            echo "</div>";
            
            echo "<div class='room-layout'>";
            for ($room = $startRoom; $room <= $endRoom; $room++) {
                if (isset($availableRooms[$room])) {
                    if (in_array($room, $bookedRooms)) {
                        echo "<div class='room booked'><i class='fas fa-bed'></i>$room</div>";
                    } else {
                        $roomData = $availableRooms[$room];
                        echo "<div class='room available' 
                                data-room-no='{$roomData['rno']}'
                                data-room-type='{$roomData['rtype']}'
                                data-room-price='{$roomData['rprice']}'>
                                <i class='fas fa-bed'></i>$room
                            </div>";
                    }
                } else {
                    echo "<div class='room not-available'><i class='fas fa-bed'></i>$room</div>";
                }
            }
            echo "</div></div>";
        }
        ?>
    </div>

    <div class="modal" id="roomModal">
        <div class="modal-content">
            <span class="close-btn"><i class="fas fa-times"></i></span>
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Room Details</h2>
            </div>
            <div class="room-detail">
                <span class="detail-label">Room Number</span>
                <span class="detail-value" id="modalRoomNo"></span>
            </div>
            <div class="room-detail">
                <span class="detail-label">Type</span>
                <span class="detail-value" id="modalRoomType"></span>
            </div>
            <div class="room-detail">
                <span class="detail-label">Price</span>
                <span class="detail-value">$<span id="modalRoomPrice"></span></span>
            </div>
           <button class="book-btn" id="bookButton" onclick="bookRoom()">
                 <i class="fas fa-check"></i> Book Now
    </button>
        </div>
    </div>

   <script>
    const modal = document.getElementById('roomModal');
    const rooms = document.querySelectorAll('.room.available');
    const closeBtn = document.querySelector('.close-btn');
    
    rooms.forEach(room => {
        room.addEventListener('click', () => {
            const roomNo = room.dataset.roomNo;
            const roomType = room.dataset.roomType;
            const roomPrice = room.dataset.roomPrice;
            
            document.getElementById('modalRoomNo').textContent = roomNo;
            document.getElementById('modalRoomType').textContent = roomType;
            document.getElementById('modalRoomPrice').textContent = roomPrice;
            
            modal.style.display = 'flex';
        });
    });
    
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    function bookRoom() {
        const roomNo = document.getElementById('modalRoomNo').textContent;
        const roomType = document.getElementById('modalRoomType').textContent;
        const roomPrice = document.getElementById('modalRoomPrice').textContent;
        
        // Create a form
        const form = document.createElement('form');
        form.method = 'GET';  // Changed to GET for simplicity
        form.action = 'booking.php';
        
        // Add room number
        const roomInput = document.createElement('input');
        roomInput.type = 'hidden';
        roomInput.name = 'room_no';
        roomInput.value = roomNo;
        form.appendChild(roomInput);
        
        // Add room type
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'room_type';
        typeInput.value = roomType;
        form.appendChild(typeInput);
        
        // Add room price
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = 'room_price';
        priceInput.value = roomPrice;
        form.appendChild(priceInput);
        
        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
</script>
</body>
</html> -->