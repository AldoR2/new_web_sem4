<?php
use App\Http\Controllers\Api\activity\AcademicCalendarController;
use App\Http\Controllers\Api\activity\AllScheduleController;
use App\Http\Controllers\Api\activity\PresenceContentController;
use App\Http\Controllers\Api\Activity\SummaryController;
use App\Http\Controllers\Api\activity\TransactionController;
use App\Http\Controllers\Api\activity\UploadProfileController;
use App\Http\Controllers\Api\Activity\ViewProfileController;
use App\Http\Controllers\Api\ActivityLecturer\AddPresenceController;
use App\Http\Controllers\Api\ActivityLecturer\AttendanceLecturerController;
use App\Http\Controllers\Api\ActivityLecturer\CheckPresenceController;
use App\Http\Controllers\Api\ActivityLecturer\DetailPresenceLecturerController;
use App\Http\Controllers\Api\ActivityLecturer\PresenceIncrementController;
use App\Http\Controllers\Api\Auth\ActivationAccountController;
use App\Http\Controllers\Api\Auth\ChangePasswordController;
use App\Http\Controllers\Api\Auth\FcmController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\NotificationController;
use App\Http\Controllers\Api\Listview\AttendanceStudentController;
use App\Http\Controllers\Api\Listview\GetLessonController;
use App\Http\Controllers\Api\Listview\LectureLecturerController;
use App\Http\Controllers\Api\Listview\LectureStudentController;
use App\Http\Controllers\Api\Listview\PresenceController;
use App\Http\Controllers\Api\Listview\PresenceLecturerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('refresh', [LoginController::class, 'refresh']);
    Route::post('forgetPassword/otp/send', [ForgetPasswordController::class, 'sendOtp']);
    Route::post('forgetPassword/otp/check', [ForgetPasswordController::class, 'checkOtp']);
    Route::post('activationAccount/otp/send', [ActivationAccountController::class, 'sendOtp']);
    Route::post('activationAccount/otp/check', [ActivationAccountController::class, 'checkOtp']);
    Route::post('activationAccount/validate', [ActivationAccountController::class, 'validateAccount']);
    Route::post('changePassword', [ChangePasswordController::class, 'changePassword']);

});

Route::middleware(['auth:api'])->group(function () {
    Route::post('auth/loginByBiometric', [LoginController::class, 'loginBiometric']);
    Route::post('auth/fcm-token', [FcmController::class, 'storeToken']);
    Route::post('auth/fcm-token/delete', [FcmController::class, 'deleteToken']);
    Route::post('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/send', [NotificationController::class, 'sendNotification']);

    Route::prefix('activityLecturer')->group(function () {
        Route::post('presence/check-edit', [CheckPresenceController::class, 'checkPresenceEdit']);
        Route::post('presence/check-upload', [CheckPresenceController::class, 'checkPresenceUpload']);
        Route::get('checkNotificationMahasiswa', [CheckPresenceController::class, 'checkRecentNotificationByMahasiswaId']);
        Route::get('checkNotificationDosen', [CheckPresenceController::class, 'checkRecentNotificationByDosenId']);
        Route::get('getMajor', [AttendanceLecturerController::class, 'showMajor']);
        Route::get('getStudent', [AttendanceLecturerController::class, 'showStudent']);
        Route::get('presence/header', [DetailPresenceLecturerController::class, 'showHeader']);
        Route::get('presence/detail', [DetailPresenceLecturerController::class, 'showDetailPresence']);
        Route::get('student/detail', [DetailPresenceLecturerController::class, 'showDetailStudent']);
        Route::get('student/information', [DetailPresenceLecturerController::class, 'showInformationStudent']);
        Route::get('presence/lastIncrement', [PresenceIncrementController::class, 'getLastIncrement']);
        Route::post('presence/uploadPresence', [AddPresenceController::class, 'uploadPresence']);
        Route::get('presence/majors', [AddPresenceController::class, 'showMajors']);
        Route::get('presence/matkuls', [AddPresenceController::class, 'showMatkuls']);
        Route::get('presence/tahunAjarans', [AddPresenceController::class, 'showTahunAjarans']);
    });
    Route::prefix('activity')->group(function () {
        Route::get('viewProfile', [ViewProfileController::class, 'show']);
        Route::get('AllScheduleStudent', [AllScheduleController::class, 'scheduleStudent']);
        Route::get('AllScheduleLecturer', [AllScheduleController::class, 'scheduleLecturer']);
        Route::get('getTransaction', [TransactionController::class, 'show']);
        Route::post('presenceActivity', [PresenceContentController::class, 'store']);
        Route::post('upProfile', [UploadProfileController::class, 'uploadProfile']);
        Route::get('getAcademicCalendar', [AcademicCalendarController::class, 'index']);
        Route::get('getNotification/mahasiswa/{mahasiswaId}', [NotificationController::class, 'getByMahasiswa']);
        Route::get('getNotification/dosen/{dosenId}', [NotificationController::class, 'getByDosen']);
        Route::get('presensi-summary/{mahasiswaId}', [SummaryController::class, 'countByMahasiswa']);
        Route::get('presensi-summary/dosen/{dosenId}', [SummaryController::class, 'countByDosen']);
    });


    Route::prefix('listview')->group(function () {
        Route::get('getLesson', [GetLessonController::class, 'getLessonStudent']);
        Route::get('getLessonLecturer', [GetLessonController::class, 'getLessonLecturer']);
        Route::get('getPresence', [PresenceController::class, 'getPresenceStudent']);
        Route::get('getPresenceLecturer', [PresenceLecturerController::class, 'showToday']);
        Route::post('updatePresence', [PresenceLecturerController::class, 'updatePresence']);
        Route::post('deletePresence', [PresenceLecturerController::class, 'deletePresence']);
        Route::get('rekapPresensi', [AttendanceStudentController::class, 'index']);
        Route::get('lectureStudent', [LectureStudentController::class, 'lecture']);
        Route::get('lectureContentStudent', [LectureStudentController::class, 'lectureContent']);
        Route::get('lectureLecturer', [LectureLecturerController::class, 'showLecture']);
        Route::get('lectureContentLecturer', [LectureLecturerController::class, 'showLectureContent']);
        Route::post('updateLecture', [LectureLecturerController::class, 'updateLecture']);
    });

});