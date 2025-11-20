#!/usr/bin/env ruby

# Set Content-Type header and start the HTML output
puts "Content-type: text/html\n\n"

# Get the Hostname using a system command
hostname = `hostname`.strip

# Get the Current Time
current_time = Time.now.strftime("%Y-%m-%d %H:%M:%S")

# Start HTML Body
puts "<html><head><title>Ruby CGI Example</title></head><body>"
puts "<h2>Dynamic Server Info (Ruby CGI)</h2>"
puts "<p>This is a simple Ruby program executed by the web server to generate HTML.</p>"

puts "<p><strong>1. Current Server Time (Ruby):</strong> #{current_time}</p>"
puts "<p><strong>2. Server Hostname:</strong> #{hostname}</p>"
puts "<p><strong>3. Executing User:</strong> #{ENV['USER']}</p>"

puts "</body></html>"

