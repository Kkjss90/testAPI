CREATE TABLE stops (
                       id SERIAL PRIMARY KEY,
                       name VARCHAR(255) NOT NULL
);

CREATE TABLE routes (
                        id SERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL
);

CREATE TABLE buses (
                       id SERIAL PRIMARY KEY,
                       route_id INT REFERENCES routes(id),
                       direction VARCHAR(255) NOT NULL
);

CREATE TABLE schedules (
                           id SERIAL PRIMARY KEY,
                           bus_id INT REFERENCES buses(id),
                           stop_id INT REFERENCES stops(id),
                           arrival_time TIME NOT NULL
);

CREATE TABLE route_stops (
                             route_id INT REFERENCES routes(id),
                             stop_id INT REFERENCES stops(id),
                             stop_order INT NOT NULL,
                             PRIMARY KEY (route_id, stop_id)
);
