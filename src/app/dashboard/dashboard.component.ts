import { Component, OnInit } from '@angular/core';
// Forms
import { FormBuilder, FormGroup } from '@angular/forms';
// File Saver
import { saveAs }  from 'file-saver';
// Models
import { Files } from 'src/app/models/files';
// Servive
import { FilesService } from '../services/files/files.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  uploadForm: any;
  filesPayload: Files;
  data: any[] = [];
  file_path: string;
  watermarkedImage: any;
  constructor(
    private filesService: FilesService,
    private formBuilder: FormBuilder
  ) { 
    this.filesPayload = new Files();
  }

  ngOnInit(): void {
    this.uploadForm = this.formBuilder.group({
      myFile: ['']
    });
    this.getImages();
  }

  watermarked = async (): Promise<void> => {
    try {
      const response = await this.filesService.watermarkedFileRequest("watermark", null);
      if (response) {
        this.watermarkedImage = response;
        console.log(this.watermarkedImage);
      }
    } catch(error) {
      console.log(error);
    }
  }
  getImages = async (): Promise<void> => {
    try {
      const response = await this.filesService.sendApiRequest("files/" + 0, null);
      if (response.status.remarks === "success") this.data = response.payload;
    } catch(error) {
       console.log(error);
    }
  }
  onFileSelect = (event) => {
    const file = event.target.files[0];
    this.uploadForm.get('myFile').setValue(file);
  }
  save = async (): Promise<void> => {
    try {
      const formData: FormData = new FormData();
      formData.append('title', this.filesPayload.fld_title);
      formData.append('category', this.filesPayload.fld_category);
      formData.append('amount', this.filesPayload.fld_amount);
      formData.append('quantity', this.filesPayload.fld_quantity);
      formData.append('myFile', this.uploadForm.get('myFile').value);
      const response = await this.filesService.createFileRequest("save", formData);
      if (response.status.remarks === "success") this.getImages();
    } catch(error) {
      console.log(error);
    }
  }
  remove = async (id: number): Promise<void> => {
    try {
      let payload: any = {};
      payload.fld_isDeleted = 1;
      const response = await this.filesService.sendApiRequest(`remove/${id}`, payload);
      if (response.status.remarks === "success") this.getImages();
    } catch(error) {
      console.log(error);
    }
  }
  download = async (id: number, title: string): Promise<void> => {
    try {
      let response: any = await this.filesService.downloadFileRequest(`download/${id}`, null);
      let blob:any = new Blob([response], { type: 'image/jpeg' });
      saveAs(blob, `${title}.jpg`);
    } catch(error) {
      console.log(error);
    }
  }
}
