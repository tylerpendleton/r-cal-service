# calendar-service

## The Problem

A User can book a Relocity Service through the Relocity mobile and web application.  They select a date, then an available time, and then book the service. 

### Facts

- We have a calendar service that can return a list of busy time intervals for the Relocity Calendar.  
- The calendar is set to a specific timezone, and you can retreive that timezone through the calendar service.
- The person booking the service is in the `America/Denver` timezone.

### Challenge

Create a method that will return a list of available start times on a particluar date.
