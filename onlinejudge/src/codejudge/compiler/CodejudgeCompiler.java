/*
 * Codejudge
 * Copyright 2012, Sankha Narayan Guria (sankha93@gmail.com)
 * Licensed under MIT License.
 *
 * Codejudge Compiler Server
 */
 
package codejudge.compiler;

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;

public class CodejudgeCompiler {
	
	public static void main(String args[]) {
		int n=0;
		try {
                       
			ServerSocket server = new ServerSocket(3029); // create a new socket to listen on
			System.out.println("Codejudge compilation server running ...");
			
                        // Server is always available
                        while(true) {
                               // n used to differentiate between the different incoming connections
				n++;
				// accept any incoming connection and process it on a new thread
				Socket s = server.accept();
                                
                                // Create a new thread for the incoming connection 
				RequestThread request = new RequestThread(s, n);
                                
				request.start();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		
	}
}
