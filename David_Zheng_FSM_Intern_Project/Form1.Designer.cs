namespace David_Zheng_FSM_Intern_Project
{
    partial class Form1
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.runButton = new System.Windows.Forms.Button();
            this.projectNameFill = new System.Windows.Forms.TextBox();
            this.projectNameLabel = new System.Windows.Forms.Label();
            this.sourcePathLabel = new System.Windows.Forms.Label();
            this.targetPathLabel = new System.Windows.Forms.Label();
            this.currentSourcePath = new System.Windows.Forms.Label();
            this.currentTargetPath = new System.Windows.Forms.Label();
            this.changePath = new System.Windows.Forms.Label();
            this.applyChange = new System.Windows.Forms.Button();
            this.sourceBrowser = new System.Windows.Forms.PictureBox();
            this.targetBrowser = new System.Windows.Forms.PictureBox();
            ((System.ComponentModel.ISupportInitialize)(this.sourceBrowser)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.targetBrowser)).BeginInit();
            this.SuspendLayout();
            // 
            // runButton
            // 
            this.runButton.Location = new System.Drawing.Point(224, 200);
            this.runButton.Name = "runButton";
            this.runButton.Size = new System.Drawing.Size(120, 23);
            this.runButton.TabIndex = 0;
            this.runButton.Text = "Run";
            this.runButton.UseVisualStyleBackColor = true;
            this.runButton.Click += new System.EventHandler(this.runButton_Click);
            // 
            // projectNameFill
            // 
            this.projectNameFill.Location = new System.Drawing.Point(122, 32);
            this.projectNameFill.Name = "projectNameFill";
            this.projectNameFill.Size = new System.Drawing.Size(222, 20);
            this.projectNameFill.TabIndex = 1;
            // 
            // projectNameLabel
            // 
            this.projectNameLabel.AutoSize = true;
            this.projectNameLabel.Font = new System.Drawing.Font("Microsoft Sans Serif", 10F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.projectNameLabel.Location = new System.Drawing.Point(12, 32);
            this.projectNameLabel.Name = "projectNameLabel";
            this.projectNameLabel.Size = new System.Drawing.Size(105, 17);
            this.projectNameLabel.TabIndex = 2;
            this.projectNameLabel.Text = "Project Name";
            // 
            // sourcePathLabel
            // 
            this.sourcePathLabel.AutoSize = true;
            this.sourcePathLabel.Font = new System.Drawing.Font("Microsoft Sans Serif", 10F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.sourcePathLabel.Location = new System.Drawing.Point(12, 76);
            this.sourcePathLabel.Name = "sourcePathLabel";
            this.sourcePathLabel.Size = new System.Drawing.Size(97, 17);
            this.sourcePathLabel.TabIndex = 3;
            this.sourcePathLabel.Text = "Source Path";
            // 
            // targetPathLabel
            // 
            this.targetPathLabel.AutoSize = true;
            this.targetPathLabel.Font = new System.Drawing.Font("Microsoft Sans Serif", 10F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.targetPathLabel.Location = new System.Drawing.Point(12, 117);
            this.targetPathLabel.Name = "targetPathLabel";
            this.targetPathLabel.Size = new System.Drawing.Size(94, 17);
            this.targetPathLabel.TabIndex = 4;
            this.targetPathLabel.Text = "Target Path";
            // 
            // currentSourcePath
            // 
            this.currentSourcePath.AutoSize = true;
            this.currentSourcePath.Location = new System.Drawing.Point(119, 78);
            this.currentSourcePath.Name = "currentSourcePath";
            this.currentSourcePath.Size = new System.Drawing.Size(128, 13);
            this.currentSourcePath.TabIndex = 6;
            this.currentSourcePath.Text = "No Current Path Selected";
            // 
            // currentTargetPath
            // 
            this.currentTargetPath.AutoSize = true;
            this.currentTargetPath.Location = new System.Drawing.Point(119, 121);
            this.currentTargetPath.Name = "currentTargetPath";
            this.currentTargetPath.Size = new System.Drawing.Size(128, 13);
            this.currentTargetPath.TabIndex = 7;
            this.currentTargetPath.Text = "No Current Path Selected";
            this.currentTargetPath.TextAlign = System.Drawing.ContentAlignment.TopCenter;
            // 
            // changePath
            // 
            this.changePath.AutoSize = true;
            this.changePath.Cursor = System.Windows.Forms.Cursors.Hand;
            this.changePath.ForeColor = System.Drawing.SystemColors.Highlight;
            this.changePath.Location = new System.Drawing.Point(144, 160);
            this.changePath.Name = "changePath";
            this.changePath.Size = new System.Drawing.Size(112, 17);
            this.changePath.TabIndex = 8;
            this.changePath.Text = "Change default paths";
            this.changePath.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            this.changePath.UseCompatibleTextRendering = true;
            this.changePath.Click += new System.EventHandler(this.changePath_Click);
            // 
            // applyChange
            // 
            this.applyChange.Location = new System.Drawing.Point(45, 200);
            this.applyChange.Name = "applyChange";
            this.applyChange.Size = new System.Drawing.Size(122, 23);
            this.applyChange.TabIndex = 9;
            this.applyChange.Text = "Apply Default Paths";
            this.applyChange.UseVisualStyleBackColor = true;
            this.applyChange.Click += new System.EventHandler(this.applyChange_Click);
            // 
            // sourceBrowser
            // 
            this.sourceBrowser.Cursor = System.Windows.Forms.Cursors.Hand;
            this.sourceBrowser.Image = global::David_Zheng_FSM_Intern_Project.Properties.Resources.Icon1;
            this.sourceBrowser.Location = new System.Drawing.Point(346, 70);
            this.sourceBrowser.Name = "sourceBrowser";
            this.sourceBrowser.Size = new System.Drawing.Size(36, 32);
            this.sourceBrowser.SizeMode = System.Windows.Forms.PictureBoxSizeMode.CenterImage;
            this.sourceBrowser.TabIndex = 10;
            this.sourceBrowser.TabStop = false;
            this.sourceBrowser.Click += new System.EventHandler(this.sourceBrowser_Click);
            // 
            // targetBrowser
            // 
            this.targetBrowser.Image = global::David_Zheng_FSM_Intern_Project.Properties.Resources.Icon1;
            this.targetBrowser.Location = new System.Drawing.Point(346, 115);
            this.targetBrowser.Name = "targetBrowser";
            this.targetBrowser.Size = new System.Drawing.Size(36, 32);
            this.targetBrowser.SizeMode = System.Windows.Forms.PictureBoxSizeMode.CenterImage;
            this.targetBrowser.TabIndex = 11;
            this.targetBrowser.TabStop = false;
            this.targetBrowser.Click += new System.EventHandler(this.targetBrowser_Click);
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(394, 246);
            this.Controls.Add(this.targetBrowser);
            this.Controls.Add(this.sourceBrowser);
            this.Controls.Add(this.applyChange);
            this.Controls.Add(this.changePath);
            this.Controls.Add(this.currentTargetPath);
            this.Controls.Add(this.currentSourcePath);
            this.Controls.Add(this.targetPathLabel);
            this.Controls.Add(this.sourcePathLabel);
            this.Controls.Add(this.projectNameLabel);
            this.Controls.Add(this.projectNameFill);
            this.Controls.Add(this.runButton);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Name = "Form1";
            this.Text = "FSM Project";
            this.Load += new System.EventHandler(this.Form1_Load);
            ((System.ComponentModel.ISupportInitialize)(this.sourceBrowser)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.targetBrowser)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Button runButton;
        private System.Windows.Forms.TextBox projectNameFill;
        private System.Windows.Forms.Label projectNameLabel;
        private System.Windows.Forms.Label sourcePathLabel;
        private System.Windows.Forms.Label targetPathLabel;
        private System.Windows.Forms.Label currentSourcePath;
        private System.Windows.Forms.Label currentTargetPath;
        private System.Windows.Forms.Label changePath;
        private System.Windows.Forms.Button applyChange;
        private System.Windows.Forms.PictureBox sourceBrowser;
        private System.Windows.Forms.PictureBox targetBrowser;
    }
}

