using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using System.Windows.Forms;

// Author: David 
namespace David_Zheng_FSM_Intern_Project
{
    // "this" = Form
    public partial class Form1 : Form
    {

        // Gives the directory of the application.exe file at initalization
        // Without the application.exe attached to the end of the path
        private string appSettingPath = AppDomain.CurrentDomain.BaseDirectory + "appSetting.txt";

        // Variables
        private string[] pathSettings;
        private Process myProcess = null;
        private FolderBrowserDialog folderBrowser = new FolderBrowserDialog();


        // Comments maded by: David Zheng
        // Checks if the general experission of the path is correct.
        // Does not provide full reliability but serves its purpose
        // Returns true if valid
        // Takes advantage of DirectoryInfo's expection handling to validate directory path
        static public bool IsValidPath(string path)
        {
            DirectoryInfo test = new DirectoryInfo(path);
            try
            {
                // Could also do GetDirectories()
                // Main goal is to take advantage of event handling.
                test.EnumerateDirectories();
            }
            catch
            {
                return false;
            }
            return true;
        }

        // Constructor
        public Form1()
        {
            InitializeComponent();

            // If the appSetting.txt file does not exist, create one for the user
            if (!File.Exists(appSettingPath))
            {
                // Implict call to StreamWriter constructor
                // StreamWriter writer = new StreamWriter(File.CreateText(appSettingPath));
                using (StreamWriter writer = File.CreateText(appSettingPath))
                {
                    writer.WriteLine("Source Path = \"" + currentSourcePath.Text + "\"");
                    writer.WriteLine("Target Path = \"" + currentTargetPath.Text + "\"");
                }

                MessageBox.Show("Warning: First Time Usage\nPlease setup default paths.",
                                "Warning",
                                MessageBoxButtons.OK, MessageBoxIcon.Warning);
            }
            else
            {
                pathSettings = readAppSetting();

                // Boolean Flags. True = Valid, False = Nope
                bool pathOne = IsValidPath(pathSettings[0]);
                bool pathTwo = IsValidPath(pathSettings[1]);

                if (pathOne && pathTwo)
                {
                    currentSourcePath.Text = pathSettings[0];
                    currentTargetPath.Text = pathSettings[1];
                }
                else
                {
                    // Simple User Friendly Error Messages
                    if (pathOne && !pathTwo)
                    {
                        MessageBox.Show("ERROR 404: PATH NOT FOUND\nPath: " + pathSettings[1] + " is not valid",
                                    "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }

                    else if (!pathOne && pathTwo)
                    {
                        MessageBox.Show("ERROR 404: PATH NOT FOUND\nPath: " + pathSettings[0] + " is not valid",
                                    "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }
                    else
                    {
                        MessageBox.Show("ERROR 404: PATH NOT FOUND\nBoth path are invalid. Please take action.",
                                    "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }

                    myProcess = System.Diagnostics.Process.Start(appSettingPath);
                }
            }
        }

        // Assume to be different from constructor
        private void Form1_Load(object sender, EventArgs e)
        {
        }

        // Recursive Function to make subdirectories
        // For a given path
        // Input Parameters: Source Path, Target Path, subDirectoryNames
        // Operates through a pre-order traversal
        private void makeSubDirectories(string source, string target, string[] directoryNames = null)
        {
            directoryNames = Directory.GetDirectories(source);

            if (directoryNames == null || directoryNames.Length == 0) return;

            for (int i = 0; i < directoryNames.Length; ++i)
            {
                // source path -  directory
                string subDirectoryName = directoryNames[i].Substring(directoryNames[i].LastIndexOf('\\'));
                Directory.CreateDirectory(target + subDirectoryName);
                makeSubDirectories(directoryNames[i], target + subDirectoryName);
            }
        }

        // "this" refers to the Form's instance
        // Executes the script to make a new folder with project name and copies folder structure
        private void runButton_Click(object sender, EventArgs e)
        {
            if (!Directory.Exists(currentTargetPath.Text + "\\" + projectNameFill.Text))
            {
                DirectoryInfo target = Directory.CreateDirectory(currentTargetPath.Text + "\\" + projectNameFill.Text);

                makeSubDirectories(currentSourcePath.Text, currentTargetPath.Text + "\\" + projectNameFill.Text);

                System.Diagnostics.Process.Start("explorer.exe", currentTargetPath.Text + "\\" + projectNameFill.Text);
                Application.Exit();
            }
            else
            {
                MessageBox.Show("Directory / Folder already exist. Opening target path....", "Task Completed"
                               , MessageBoxButtons.OK, MessageBoxIcon.Information);
                Process.Start("explorer.exe", currentTargetPath.Text);
            }
        }

        // Feature functionality
        // Allows user to change the appSetting.txt file

        // Future:  
        // A simple popup window will be used to prompt user to change path by copy and paste
        // To avoid any contact with the txt file directly
        private void changePath_Click(object sender, EventArgs e)
        {
            // Opens the txt file
            // Simple check to avoid opening the multiple times due to multiple clicks
            if (myProcess != null && !myProcess.HasExited) return;
            else myProcess = System.Diagnostics.Process.Start(appSettingPath);
        }


        // Helper function
        // Reads the path from appSetting.txt
        // Reads an array of strings with the paths as an element
        private string[] readAppSetting()
        {
            // Each element is a line from the txt file
            string[] fileLines = File.ReadAllLines(appSettingPath);

            string[] result = new string[2];

            // Inefficient, but useable for situation due to small data size
            for (int i = 0; i < fileLines.Length; ++i)
            {
                int startPos = fileLines[i].IndexOf('\"');
                int endPos = fileLines[i].LastIndexOf('\"');
                int offset = 1;

                result[i] = fileLines[i].Substring(startPos + offset, endPos - startPos - offset);
            }
            return result;
        }

        // Restart a new instance with updated appSettings
        private void applyChange_Click(object sender, EventArgs e)
        {
            string[] newAppSetting = readAppSetting();
            bool pathOne = IsValidPath(newAppSetting[0]); // Boolean Flags. True = Valid, False = Nope
            bool pathTwo = IsValidPath(newAppSetting[1]);

            if (pathOne && pathTwo)
            {
                currentSourcePath.Text = newAppSetting[0];
                currentTargetPath.Text = newAppSetting[1];
            }
            else
            {

                if (pathOne && !pathTwo)
                {
                    MessageBox.Show("ERROR 404: PATH NOT FOUND\nPath: " + newAppSetting[1] + " is not valid",
                                "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }

                else if (!pathOne && pathTwo)
                {
                    MessageBox.Show("ERROR 404: PATH NOT FOUND\nPath: " + newAppSetting[0] + " is not valid",
                                "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
                else
                {
                    MessageBox.Show("ERROR 404: PATH NOT FOUND\nBoth path are invalid. Please take action.",
                                "ERROR", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }

                if (myProcess != null && !myProcess.HasExited) return;
                else myProcess = System.Diagnostics.Process.Start(appSettingPath);
            }
            //Application.Restart();
        }

        // Misc Functions
        private void sourceBrowser_Click(object sender, EventArgs e)
        {
            if(folderBrowser.ShowDialog() == DialogResult.OK)
            {
                currentSourcePath.Text = folderBrowser.SelectedPath;
            }
        }

        private void targetBrowser_Click(object sender, EventArgs e)
        {
            if(folderBrowser.ShowDialog() == DialogResult.OK)
            {
                currentTargetPath.Text = folderBrowser.SelectedPath;
            }
        }
    }
}
